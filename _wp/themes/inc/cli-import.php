<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) return;

require_once get_template_directory() . '/inc/class-template-importer.php';

/**
 * Template Corporate コンテンツインポーター
 *
 * ## EXAMPLES
 *
 *   wp template import --type=case --dry-run
 *   wp template import --type=topics
 *   wp template import --type=all --update
 *   wp template import --type=news --key=news-sample-2025
 */
class Template_Import_Command {

	/**
	 * $*_list を WordPress へ一括投入する
	 *
	 * ## OPTIONS
	 *
	 * [--type=<type>]
	 * : 投入対象。case / topics / news / service / works / recruit / all
	 *
	 * [--dry-run]
	 * : 実際には書き込まない
	 *
	 * [--update]
	 * : 既存投稿がある場合に上書き更新する
	 *
	 * [--key=<key>]
	 * : 特定の slug のみ処理する
	 *
	 * [--status=<status>]
	 * : post_status を指定（デフォルト: draft）
	 *
	 * [--sideload-unused]
	 * : ACF 参照外の画像もメディアライブラリに追加（デフォルト: false）
	 *
	 * @when after_wp_load
	 */
	public function import( array $args, array $assoc_args ): void {
		$inc_path = get_template_directory() . '/assets/inc/';

		$type            = $assoc_args['type']   ?? '';
		$dry_run         = isset( $assoc_args['dry-run'] );
		$do_update       = isset( $assoc_args['update'] );
		$target_key      = $assoc_args['key']    ?? null;
		$status          = $assoc_args['status'] ?? 'draft';
		$sideload_unused = isset( $assoc_args['sideload-unused'] );

		$valid_types = [ 'case', 'topics', 'news', 'service', 'works', 'recruit', 'all' ];
		if ( ! in_array( $type, $valid_types, true ) ) {
			WP_CLI::error( '--type は case / topics / news / service / works / recruit / all のいずれかを指定してください' );
			return;
		}

		$options = [
			'dry_run'         => $dry_run,
			'update'          => $do_update,
			'target_key'      => $target_key,
			'post_status'     => $status,
			'sideload_unused' => $sideload_unused,
		];

		$importer  = new TemplateImporter();
		$all_items = [];
		$total     = [ 'success' => 0, 'skipped' => 0, 'skipped_dummy' => 0, 'sideloaded_unused' => 0, 'errors' => [] ];

		$cpt_map = [
			'case'    => [ 'file' => 'value/page/case.php',    'var' => 'case_list',    'label' => 'Case' ],
			'topics'  => [ 'file' => 'value/page/topics.php',  'var' => 'topics_list',  'label' => 'Topics' ],
			'news'    => [ 'file' => 'value/page/news.php',    'var' => 'news_list',    'label' => 'News' ],
			'service' => [ 'file' => 'value/page/service.php', 'var' => 'service_list', 'label' => 'Service' ],
			'works'   => [ 'file' => 'value/page/works.php',   'var' => 'works_list',   'label' => 'Works' ],
			'recruit' => [ 'file' => 'value/page/recruit.php', 'var' => 'recruit_list', 'label' => 'Recruit' ],
		];

		foreach ( $cpt_map as $cpt_type => $cpt ) {
			if ( ! in_array( $type, [ $cpt_type, 'all' ], true ) ) continue;

			WP_CLI::log( "── {$cpt['label']} をインポート中..." );
			require $inc_path . $cpt['file'];
			$items  = $$cpt['var'] ?? [];
			$result = $importer->import( $items, $options );
			$total['success']           += $result['success'];
			$total['skipped']           += $result['skipped'];
			$total['skipped_dummy']     += $result['skipped_dummy'];
			$total['sideloaded_unused'] += $result['sideloaded_unused'];
			$total['errors']             = array_merge( $total['errors'], $result['errors'] );
			$all_items                   = array_merge( $all_items, $items );
		}

		if ( ! $dry_run && ! empty( $all_items ) ) {
			WP_CLI::log( '── 関連投稿を解決中...' );
			$importer->resolveRelations( $all_items );
		}

		WP_CLI::log( '' );
		WP_CLI::success( sprintf(
			'完了: 成功=%d / スキップ=%d / ダミースキップ=%d / 未参照画像sideload=%d / エラー=%d',
			$total['success'],
			$total['skipped'],
			$total['skipped_dummy'],
			$total['sideloaded_unused'],
			count( $total['errors'] )
		) );

		foreach ( $total['errors'] as $err ) {
			WP_CLI::warning( $err );
		}
	}
}

WP_CLI::add_command( 'template', 'Template_Import_Command' );
