<?php
if ( ! defined( 'ABSPATH' ) ) return;

add_action( 'admin_menu', function () {
	add_menu_page(
		'コンテンツインポーター',
		'インポーター',
		'manage_options',
		'template-importer',
		'template_importer_page',
		'dashicons-upload',
		99
	);
} );

function template_importer_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'アクセス権限がありません。' );
	}

	$result   = null;
	$executed = false;

	$valid_types = [ 'case', 'topics', 'news', 'service', 'works', 'recruit', 'all' ];

	if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
		check_admin_referer( 'template_import' );

		set_time_limit( 0 );

		$type            = sanitize_text_field( $_POST['type'] ?? 'all' );
		$dry_run         = ! empty( $_POST['dry_run'] );
		$do_update       = ! empty( $_POST['do_update'] );
		$sideload_unused = ! empty( $_POST['sideload_unused'] );

		if ( ! in_array( $type, $valid_types, true ) ) {
			$type = 'all';
		}

		require_once get_template_directory() . '/inc/class-template-importer.php';

		global $inc_path;
		add_filter( 'intermediate_image_sizes_advanced', '__return_empty_array' );

		$importer  = new TemplateImporter();
		$all_items = [];
		$total     = [ 'success' => 0, 'skipped' => 0, 'skipped_dummy' => 0, 'sideloaded_unused' => 0, 'errors' => [] ];
		$options   = [
			'dry_run'         => $dry_run,
			'update'          => $do_update,
			'post_status'     => 'draft',
			'sideload_unused' => $sideload_unused,
		];

		$cpt_map = [
			'case'    => [ 'file' => 'value/page/case.php',    'var' => 'case_list' ],
			'topics'  => [ 'file' => 'value/page/topics.php',  'var' => 'topics_list' ],
			'news'    => [ 'file' => 'value/page/news.php',    'var' => 'news_list' ],
			'service' => [ 'file' => 'value/page/service.php', 'var' => 'service_list' ],
			'works'   => [ 'file' => 'value/page/works.php',   'var' => 'works_list' ],
			'recruit' => [ 'file' => 'value/page/recruit.php', 'var' => 'recruit_list' ],
		];

		foreach ( $cpt_map as $cpt_type => $cpt ) {
			if ( ! in_array( $type, [ $cpt_type, 'all' ], true ) ) continue;

			require $inc_path . $cpt['file'];
			$items = $$cpt['var'] ?? [];
			$r = $importer->import( $items, $options );
			$total['success']           += $r['success'];
			$total['skipped']           += $r['skipped'];
			$total['skipped_dummy']     += $r['skipped_dummy'];
			$total['sideloaded_unused'] += $r['sideloaded_unused'];
			$total['errors']             = array_merge( $total['errors'], $r['errors'] );
			$all_items                   = array_merge( $all_items, $items );
		}

		if ( ! $dry_run && ! empty( $all_items ) ) {
			$importer->resolveRelations( $all_items );
		}

		$result   = $total;
		$executed = true;
	}
	?>
	<div class="wrap">
		<h1>コンテンツインポーター</h1>
		<p>$case_list / $topics_list / $news_list / $service_list / $works_list / $recruit_list を WordPress カスタムポストタイプへ投入します。</p>

		<?php if ( $executed && $result !== null ) : ?>
			<?php if ( $result['success'] > 0 ) : ?>
				<div class="notice notice-success">
					<p>✔ 成功: <?php echo (int) $result['success']; ?> 件</p>
				</div>
			<?php endif; ?>

			<?php if ( $result['skipped'] > 0 ) : ?>
				<div class="notice notice-info">
					<p>– スキップ: <?php echo (int) $result['skipped']; ?> 件（既存投稿あり・--update なし）</p>
				</div>
			<?php endif; ?>

			<?php if ( $result['skipped_dummy'] > 0 ) : ?>
				<div class="notice notice-info">
					<p>– ダミースキップ: <?php echo (int) $result['skipped_dummy']; ?> 件（静的ダミーエントリ）</p>
				</div>
			<?php endif; ?>

			<?php if ( $result['sideloaded_unused'] > 0 ) : ?>
				<div class="notice notice-success">
					<p>+ 未参照画像 sideload: <?php echo (int) $result['sideloaded_unused']; ?> 件</p>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $result['errors'] ) ) : ?>
				<div class="notice notice-error">
					<p>✖ エラー: <?php echo count( $result['errors'] ); ?> 件</p>
					<ul>
						<?php foreach ( $result['errors'] as $err ) : ?>
							<li><?php echo esc_html( $err ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<form method="post">
			<?php wp_nonce_field( 'template_import' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">対象タイプ</th>
					<td>
						<label><input type="radio" name="type" value="all" checked> すべて（case + topics + news + service + works + recruit）</label><br>
						<label><input type="radio" name="type" value="case"> Case のみ</label><br>
						<label><input type="radio" name="type" value="topics"> Topics のみ</label><br>
						<label><input type="radio" name="type" value="news"> News のみ</label><br>
						<label><input type="radio" name="type" value="service"> Service のみ</label><br>
						<label><input type="radio" name="type" value="works"> Works のみ</label><br>
						<label><input type="radio" name="type" value="recruit"> Recruit のみ</label>
					</td>
				</tr>
				<tr>
					<th scope="row">ドライラン</th>
					<td>
						<label>
							<input type="checkbox" name="dry_run" value="1" checked>
							実際には書き込まない（確認用）
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">既存投稿を更新</th>
					<td>
						<label>
							<input type="checkbox" name="do_update" value="1">
							slug が一致する既存投稿を上書き更新する
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">未参照画像もメディアに追加</th>
					<td>
						<label>
							<input type="checkbox" name="sideload_unused" value="1">
							ACF から参照されていない画像もメディアライブラリに登録する
						</label>
					</td>
				</tr>
			</table>

			<p class="submit">
				<button type="submit" class="button button-primary">インポート実行</button>
			</p>
		</form>
	</div>
	<?php
}
