<?php
/**
 * TemplateImporter
 * $case_list / $topics_list / $news_list / $service_list / $works_list / $recruit_list の配列を
 * WordPress カスタムポストタイプへ一括投入する。
 */
class TemplateImporter {

	const META_SOURCE_PATH = '_import_source_path';

	private string $image_base_dir;

	public function __construct() {
		$this->image_base_dir = get_template_directory() . '/assets/image/';
	}

	// ── メイン ──────────────────────────────────────────

	/**
	 * @param array $items   $*_list の配列
	 * @param array $options ['dry_run', 'update', 'target_key', 'post_status']
	 * @return array ['success' => int, 'skipped' => int, 'errors' => array]
	 */
	public function import( array $items, array $options = [] ): array {
		$dry_run    = ! empty( $options['dry_run'] );
		$do_update  = ! empty( $options['update'] );
		$target_key = $options['target_key'] ?? null;
		$status     = $options['post_status'] ?? 'draft';

		$result = [ 'success' => 0, 'skipped' => 0, 'skipped_dummy' => 0, 'sideloaded_unused' => 0, 'errors' => [] ];

		foreach ( $items as $item ) {
			$slug = $item['slug'] ?? '';
			if ( $slug === '' || strpos( $slug, 'detail.php' ) !== false || strpos( $slug, '?id=' ) !== false ) {
				$result['skipped_dummy']++;
				if ( $options['verbose'] ?? false ) {
					$this->log( '[skip] static dummy entry: title=' . ( $item['title']['main'] ?? '' ) . ', slug=' . $slug );
				}
				continue;
			}

			$key = $item['slug'] ?? null;
			if ( empty( $key ) ) {
				$result['errors'][] = 'slug が見つかりません: ' . ( $item['title']['main'] ?? '?' );
				continue;
			}

			if ( $target_key && $key !== $target_key ) {
				continue;
			}

			$post_type = $item['post_type'] ?? 'post';
			$existing  = $this->findPostBySlug( $key, $post_type );

			if ( $existing && ! $do_update ) {
				$this->log( "SKIP [{$key}] 既存投稿あり（--update なし）" );
				$result['skipped']++;
				continue;
			}

			if ( $dry_run ) {
				$action = $existing ? 'UPDATE' : 'CREATE';
				$this->log( "DRY [{$action}] {$key} / " . ( $item['title']['main'] ?? '' ) );
				$result['success']++;
				continue;
			}

			try {
				$item['post_status'] = $status;
				$post_id = $this->createOrUpdatePost( $item, [ 'existing_id' => $existing ] );

				if ( ! empty( $item['taxonomy'] ) ) {
					$this->setTaxonomies( $post_id, $post_type, $item['taxonomy'] );
				}

				$this->setFeaturedImage( $post_id, $item );
				$this->updateAcfFields( $post_id, $item );

				if ( $options['sideload_unused'] ?? false ) {
					$result['sideloaded_unused'] += $this->sideloadUnusedImages( $post_id, $item );
				}

				$this->log( ( $existing ? 'UPDATE' : 'CREATE' ) . " [{$key}] post_id={$post_id}" );
				$result['success']++;
			} catch ( \Exception $e ) {
				$msg = "ERROR [{$key}] " . $e->getMessage();
				$this->log( $msg, 'warning' );
				$result['errors'][] = $msg;
			}
		}

		return $result;
	}

	/**
	 * 2パス目: slug から post_id を解決して relation フィールドを更新する
	 */
	public function resolveRelations( array $items ): void {
		$key_map = [];
		foreach ( $items as $item ) {
			$key       = $item['slug'] ?? null;
			$post_type = $item['post_type'] ?? 'post';
			if ( ! $key ) continue;
			$id = $this->findPostBySlug( $key, $post_type );
			if ( $id ) $key_map[ $key ] = $id;
		}

		foreach ( $items as $item ) {
			$key       = $item['slug'] ?? null;
			$post_type = $item['post_type'] ?? 'post';
			if ( ! $key ) continue;
			$post_id = $key_map[ $key ] ?? null;
			if ( ! $post_id ) continue;

			foreach ( $item as $field_name => $field_value ) {
				if ( ! is_array( $field_value ) || empty( $field_value['relation']['keys'] ) ) continue;
				$keys = $field_value['relation']['keys'];
				if ( ! is_array( $keys ) || empty( array_filter( $keys ) ) ) continue;

				$ids = [];
				foreach ( $keys as $rel_key ) {
					if ( ! empty( $key_map[ $rel_key ] ) ) {
						$ids[] = $key_map[ $rel_key ];
					}
				}

				if ( ! empty( $ids ) ) {
					update_field( $field_name, $ids, $post_id );
					$this->log( "RELATION [{$key}] {$field_name} → " . implode( ',', $ids ) );
				}
			}
		}
	}

	// ── 投稿操作 ──────────────────────────────────────────

	private function findPostBySlug( string $key, string $post_type ): ?int {
		$query = new WP_Query( [
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'name'           => $key,
		] );
		if ( $query->have_posts() ) {
			return $query->posts[0]->ID;
		}
		return null;
	}

	private function createOrUpdatePost( array $item, array $opts = [] ): int {
		$existing_id = $opts['existing_id'] ?? null;
		$post_data   = [
			'post_type'   => $item['post_type'] ?? 'post',
			'post_status' => $item['post_status'] ?? 'draft',
			'post_title'  => $item['title']['main'] ?? '',
			'post_name'   => $item['slug'],
		];

		if ( $existing_id ) {
			$post_data['ID'] = $existing_id;
			$post_id = wp_update_post( $post_data, true );
		} else {
			$post_id = wp_insert_post( $post_data, true );
		}

		if ( is_wp_error( $post_id ) ) {
			throw new \Exception( $post_id->get_error_message() );
		}

		return $post_id;
	}

	// ── タクソノミー ──────────────────────────────────────

	private function setTaxonomies( int $post_id, string $post_type, array $taxonomies ): void {
		foreach ( $taxonomies as $key => $terms ) {
			$taxonomy = $post_type . '_' . $key;
			$slugs    = [];
			foreach ( (array) $terms as $term ) {
				$slug = '';
				if ( is_array( $term ) ) {
					$slug = $term['slug'] ?? '';
				} elseif ( is_string( $term ) ) {
					$slug = $term;
				}
				$slug = trim( $slug );
				if ( $slug !== '' ) $slugs[] = $slug;
			}
			if ( ! empty( $slugs ) ) {
				wp_set_object_terms( $post_id, $slugs, $taxonomy, false );
			}
		}
	}

	// ── 画像 ──────────────────────────────────────────────

	/**
	 * setValueImage() の戻り値または文字列パスを attachment_id に解決する。
	 * ダミー画像（_dummy を含む）はスキップして 0 を返す。
	 */
	private function resolveImage( $image_value ): int {
		$dbg_src = is_array( $image_value ) ? ( $image_value['sizes']['large'] ?? json_encode( $image_value ) ) : (string) $image_value;
		$this->log( 'resolveImage input: ' . $dbg_src );

		if ( empty( $image_value ) ) {
			$this->log( 'resolveImage: empty value -> return 0', 'warning' );
			return 0;
		}

		if ( is_array( $image_value ) ) {
			$src = $image_value['src'] ?? $image_value['sizes']['large'] ?? '';
		} else {
			$src = (string) $image_value;
		}

		if ( empty( $src ) ) {
			$this->log( 'resolveImage: src empty -> return 0', 'warning' );
			return 0;
		}

		if ( strpos( $src, '_dummy' ) !== false ) {
			$this->log( 'resolveImage: dummy image -> skip', 'info' );
			return 0;
		}

		if ( is_numeric( $src ) && (int) $src > 0 ) return (int) $src;

		if ( strpos( $src, 'http' ) === 0 ) {
			return $this->sideloadImageFromUrl( $src );
		}

		$abs_path = $this->image_base_dir . ltrim( $src, '/' );
		return $this->sideloadImageFromPath( $abs_path );
	}

	private function sideloadImageFromUrl( string $url ): int {
		$existing = $this->findAttachmentBySourcePath( $url );
		if ( $existing ) return $existing;

		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attachment_id = media_sideload_image( $url, 0, null, 'id' );
		if ( is_wp_error( $attachment_id ) ) {
			$this->log( 'sideload URL failed: ' . $url . ' / ' . $attachment_id->get_error_message(), 'warning' );
			return 0;
		}
		update_post_meta( $attachment_id, self::META_SOURCE_PATH, $url );
		return $attachment_id;
	}

	private function sideloadImageFromPath( string $abs_path ): int {
		if ( ! file_exists( $abs_path ) ) {
			$this->log( "画像ファイルが見つかりません: {$abs_path}", 'warning' );
			return 0;
		}

		$existing = $this->findAttachmentBySourcePath( $abs_path );
		if ( $existing ) return $existing;

		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$filename = basename( $abs_path );
		$contents = file_get_contents( $abs_path );
		if ( $contents === false ) {
			$this->log( "ファイル読み込み失敗: {$abs_path}", 'warning' );
			return 0;
		}

		$upload = wp_upload_bits( $filename, null, $contents );
		if ( ! empty( $upload['error'] ) ) {
			$this->log( "wp_upload_bits 失敗: " . $upload['error'], 'warning' );
			return 0;
		}

		$wp_filetype = wp_check_filetype( $filename );
		$attachment  = [
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name( $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		];
		$attach_id = wp_insert_attachment( $attachment, $upload['file'] );
		if ( is_wp_error( $attach_id ) ) {
			$this->log( "wp_insert_attachment 失敗: " . $attach_id->get_error_message(), 'warning' );
			return 0;
		}

		$attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		update_post_meta( $attach_id, self::META_SOURCE_PATH, $abs_path );

		return $attach_id;
	}

	private function findAttachmentBySourcePath( string $path ): ?int {
		$query = new WP_Query( [
			'post_type'      => 'attachment',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_query'     => [
				[ 'key' => self::META_SOURCE_PATH, 'value' => $path ],
			],
		] );
		if ( ! $query->have_posts() ) return null;

		$attach_id = $query->posts[0]->ID;
		$file      = get_attached_file( $attach_id );
		if ( ! $file || ! file_exists( $file ) ) {
			$this->log( "broken attachment detected id={$attach_id} file=" . ( $file ?: 'null' ) . " -> delete & re-sideload", 'warning' );
			wp_delete_attachment( $attach_id, true );
			return null;
		}
		return $attach_id;
	}

	private function setFeaturedImage( int $post_id, array $item ): void {
		$image = $item['thumbnail']['image'] ?? $item['image'] ?? null;
		$this->log( "setFeaturedImage post_id={$post_id} has_image=" . ( $image ? 'yes' : 'no' ) );
		$attach_id = $this->resolveImage( $image );
		$this->log( "setFeaturedImage post_id={$post_id} attach_id={$attach_id}" );
		if ( $attach_id > 0 ) {
			set_post_thumbnail( $post_id, $attach_id );
		}
	}

	// ── ACF フィールド ────────────────────────────────────

	private function updateAcfFields( int $post_id, array $item ): void {
		$this->log( "updateAcfFields start post_id={$post_id} slug=" . ( $item['slug'] ?? '?' ) );

		$skip_keys = [ 'post_type', 'post_status', 'date', 'class', 'id', 'slug', 'section_mode', 'parent', 'pankuzu', 'taxonomy', 'nav' ];

		$post_type = $item['post_type'] ?? 'post';

		foreach ( $item as $field_name => $value ) {
			if ( in_array( $field_name, $skip_keys, true ) ) continue;

			if ( $field_name === 'body' && is_array( $value ) ) {
				$processed  = $this->processBodySections( $post_id, $value );
				$field_key  = $this->resolveFieldKey( 'body', $post_type );
				update_field( $field_key, $processed, $post_id );
				continue;
			}

			if ( is_array( $value ) && ! empty( $value['relation']['keys'] ) ) {
				continue;
			}

			$value     = $this->processImageFieldsRecursive( $value, $post_id );
			$field_key = $this->resolveFieldKey( $field_name, $post_type );
			update_field( $field_key, $value, $post_id );
		}

		$this->log( "updateAcfFields done post_id={$post_id}" );
	}

	/**
	 * ポストタイプに合致するフィールドグループから $name のフィールドキーを取得する。
	 * 見つからない場合は $name をそのまま返す（ACF のフォールバック）。
	 */
	private function resolveFieldKey( string $name, string $post_type ): string {
		$groups = acf_get_field_groups( [ 'post_type' => $post_type ] );
		foreach ( $groups as $group ) {
			$fields = acf_get_local_fields( $group['key'] );
			foreach ( (array) $fields as $field ) {
				if ( ( $field['name'] ?? '' ) === $name ) {
					return $field['key'];
				}
			}
		}
		return $name;
	}

	private function processBodySections( int $post_id, array $body ): array {
		$result = [];
		foreach ( $body as $section_key => $section ) {
			if ( ! is_array( $section ) ) {
				$result[ $section_key ] = $section;
				continue;
			}
			$processed_section = [];
			foreach ( $section as $key => $val ) {
				if ( $key === 'box' && is_array( $val ) ) {
					$processed_section['box'] = array_map(
						fn( $box ) => $this->processBoxItem( $box, $post_id ),
						$val
					);
				} else {
					$processed_section[ $key ] = $val;
				}
			}
			$result[ $section_key ] = $processed_section;
		}
		return $result;
	}

	private function processBoxItem( array $box, int $post_id ): array {
		$layout = $box['acf_fc_layout'] ?? '';
		$result = [ 'acf_fc_layout' => $layout ];

		$image_fields = [ 'image', 'youtube' ];
		if ( in_array( $layout, $image_fields, true ) && isset( $box[ $layout ] ) ) {
			if ( $layout === 'image' ) {
				$result['image'] = $this->resolveImage( $box['image'] );
			} else {
				$result[ $layout ] = $this->processImageFieldsRecursive( $box[ $layout ], $post_id );
			}
			foreach ( $box as $key => $val ) {
				if ( $key === 'acf_fc_layout' || $key === $layout ) continue;
				$result[ $key ] = $this->processImageFieldsRecursive( $val, $post_id );
			}
			return $result;
		}

		foreach ( $box as $key => $val ) {
			if ( $key === 'acf_fc_layout' ) continue;
			$result[ $key ] = $this->processImageFieldsRecursive( $val, $post_id );
		}
		return $result;
	}

	/**
	 * 再帰的に setValueImage() の戻り値を attachment_id に変換する
	 */
	private function processImageFieldsRecursive( $value, int $post_id ) {
		if ( ! is_array( $value ) ) return $value;

		// setValueImage() の戻り値パターン: ['title' => '...', 'sizes' => ['large' => '...', ...]]
		if ( isset( $value['title'] ) && isset( $value['sizes'] ) ) {
			return $this->resolveImage( $value );
		}

		$result = [];
		foreach ( $value as $k => $v ) {
			$result[ $k ] = $this->processImageFieldsRecursive( $v, $post_id );
		}
		return $result;
	}

	// ── 未参照画像 sideload ────────────────────────────────

	private function sideloadUnusedImages( int $post_id, array $item ): int {
		$post_type = $item['post_type'] ?? '';
		$slug      = $item['slug'] ?? '';
		if ( ! $post_type || ! $slug ) return 0;

		$dir = get_template_directory() . "/assets/image/import/{$post_type}/{$slug}/";
		if ( ! is_dir( $dir ) ) return 0;

		$files = glob( $dir . '*' );
		if ( ! $files ) return 0;

		$extensions = [ 'png', 'webp', 'jpg', 'jpeg' ];
		$count      = 0;

		foreach ( $files as $file_path ) {
			$ext = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
			if ( ! in_array( $ext, $extensions, true ) ) continue;

			$existing = $this->findAttachmentBySourcePath( $file_path );
			if ( $existing ) continue;

			$attach_id = $this->sideloadImageFromPath( $file_path );
			if ( $attach_id > 0 ) {
				$sideloaded = get_post_meta( $post_id, '_unused_sideloaded_image', true );
				if ( ! is_array( $sideloaded ) ) $sideloaded = [];
				$rel_path = "import/{$post_type}/{$slug}/" . basename( $file_path );
				if ( ! in_array( $rel_path, $sideloaded, true ) ) {
					$sideloaded[] = $rel_path;
					update_post_meta( $post_id, '_unused_sideloaded_image', $sideloaded );
				}
				$count++;
			}
		}

		return $count;
	}

	// ── ログ ──────────────────────────────────────────────

	private function log( string $message, string $level = 'info' ): void {
		error_log( '[TemplateImporter][' . $level . '] ' . $message );

		if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) return;
		switch ( $level ) {
			case 'warning':
				WP_CLI::warning( $message );
				break;
			case 'error':
				WP_CLI::error( $message, false );
				break;
			default:
				WP_CLI::log( $message );
		}
	}
}
