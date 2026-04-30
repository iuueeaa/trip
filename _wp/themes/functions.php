<?php
// TODO: Bogo多言語化の時、共通部分・お問い合わせメールの多言語対応、ACFの対応(cf:continewm)

$root_path  = get_template_directory();
$local_path       = get_template_directory_uri();
$link_path        = home_url();
$lang = $lang ?? 'ja';
$wpflg = true;
$image_path       = $local_path . '/assets/image/';
$inc_path         = $root_path . '/assets/inc/';
$icon_path        = $image_path . 'icon/sprite.svg#';
$site_url = home_url();
$protocol = (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
require_once $inc_path . 'value/_common.php';
require_once $inc_path . 'function/index.php';
require_once($inc_path . "value/{$lang}.php");
$fixedpage_arr = array(
	'top' => array('id' => 375, 'name' => 'TOP', 'rank' => 5),
	'about' => array('id' => 435, 'name' => 'ABOUT', 'rank' => 5),
	'privacy' => array('id' => 3, 'name' => 'PRIVACY', 'rank' => 11),
);
$feedpost = array();


/*------------------------------------------------
  1. ACFオプションページ・カスタムフィールド定義
------------------------------------------------*/
if (function_exists('acf_add_options_page')) {
	acf_add_options_page([
		'page_title' => '初期設定',
		'menu_title' => '初期設定',
		'menu_slug'  => 'initsetting',
		'position'   => 3,
		'post_id'    => 'setting',
	]);
}



/*------------------------------------------------
  2. 管理画面カスタマイズ
------------------------------------------------*/
// マニュアルページ追加
function mt_add_pages() {
	add_menu_page(
		'マニュアル',           // ページタイトル
		'マニュアル',           // メニュータイトル
		'edit_posts',           // 権限
		'manual',               // スラッグ
		'show_manual',          // コールバック関数
		'dashicons-book-alt',   // アイコン
		2                       // メニュー位置
	);
}
function show_manual() {
	echo '<div class="wrap">';
	include get_template_directory() . '/manual.php';
	echo '</div>';
}
add_action('admin_menu', 'mt_add_pages');


// 固定ページを管理メニュー追加
add_action('admin_menu', 'my_admin_menu');
function my_admin_menu() {
	global $fixedpage_arr;
	foreach ($fixedpage_arr as $value) {
		add_menu_page($value['name'], $value['name'], 'edit_pages', '/post.php?post=' . $value['id'] . '&action=edit', '', '', $value['rank']);
	}
}


// 管理画面にcss/jsを適用する
add_action('admin_print_styles', 'my_admin_style');
function my_admin_style() {
	wp_enqueue_style('my_admin_style', get_template_directory_uri() . '/assets/css/wpadmin.css');
}

// 権限付与やadmin_bar非表示
$role_object = get_role('editor');
$role_object->add_cap('manage_privacy_options', true);
$role_object->add_cap('manage_options');
add_filter('allow_major_auto_core_updates', '__return_true');/* メジャーアップグレード自動更新有効 */
add_filter('auto_update_plugin', '__return_true');/* プラグイン自動更新を有効化 */
remove_action('wp_head', 'print_emoji_detection_script', 7);/* WPのデフォルトCSSなど削除用 */
remove_action('wp_print_styles', 'print_emoji_styles', 10);
add_filter('show_admin_bar', '__return_false');


// WYSIWYG（ビジュアルエディタ／リッチエディタ／Gutenberg含む）の削除
add_filter('use_block_editor_for_post', '__return_false', 10); // 投稿
add_filter('use_block_editor_for_page', '__return_false', 10); // 固定ページ
add_filter('use_block_editor_for_post_type', '__return_false', 10); // CPT
add_filter('user_can_richedit', function ($can_richedit) {
	global $typenow;
	$allowed_types = array('my_cpt'); // 許可したい投稿タイプを追加
	if (in_array($typenow, $allowed_types, true)) {
		return true;  // 許可する
	}
	return false; // それ以外はテキストエディタのみ
});


// --- タイトル文字数カウント ---
add_action('admin_head-post-new.php', 'count_title_characters');
add_action('admin_head-post.php', 'count_title_characters');
function count_title_characters() { ?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			//全角を1、半角を0.5として数える
			function count_zen_han_characters(str) {
				len = 0;
				str = escape(str);
				for (i = 0; i < str.length; i++, len++) {
					if (str.charAt(i) == "%") {
						if (str.charAt(++i) == "u") {
							i += 3;
							len++;
						}
						i++;
					}
				}
				return len / 2;
			}

			//in_selの文字数をカウントしてout_selに出力する
			function count_characters(in_sel, out_sel) {
				$(out_sel).html(count_zen_han_characters($(in_sel).val()));
			}

			//ページ表示に表示エリアを出力
			$('#titlewrap').after('<div style="position:absolute;top:-24px;right:0;color:#666;background-color:#f7f7f7;padding:1px 2px;border-radius:5px;border:1px solid #ccc;">文字数<span class="wp-title-count" style="margin-left:5px;">0</span></div>');

			//ページ表示時に数える
			count_characters('#title', '.wp-title-count');

			//入力フォーム変更時に数える
			$('#title').bind("keydown keyup keypress change", function() {
				count_characters('#title', '.wp-title-count');
			});

		});
	</script>
<?php
}


// --- 投稿一覧画面での情報追加 ---

// foreach ($custompostarray as $cpt) {
// 	// 投稿ページの設定
// 	$post_type = $cpt['slug'];
// 	add_filter("manage_edit-{$post_type}_columns", function ($columns) {
// 		$position = array_search('title', array_keys($columns));
// 		if ($position === false) $position = 0;

// 		$new_columns = [
// 			'custom_image' => '画像',
// 			'custom_text'  => 'テキスト'
// 		];

// 		$columns_before = array_slice($columns, 0, $position + 1, true); // タイトルまで
// 		$columns_after  = array_slice($columns, $position + 1, null, true); // タイトル以降
// 		$columns = $columns_before + $new_columns + $columns_after;
// 		return $columns;
// 	});
// 	add_action("manage_{$post_type}_posts_custom_column", 'show_custom_columns', 10, 2);
// }
add_filter('manage_pages_columns', function ($columns) {
	// 固定ページの設定
	$position = array_search('title', array_keys($columns));
	if ($position === false) $position = 0;

	$new_columns = [
		'custom_image' => '画像',
		'custom_text'  => 'テキスト'
	];

	$columns_before = array_slice($columns, 0, $position + 1, true); // タイトルまで
	$columns_after  = array_slice($columns, $position + 1, null, true); // タイトル以降
	$columns = $columns_before + $new_columns + $columns_after;
	return $columns;
});
add_action('manage_pages_custom_column', 'show_custom_columns', 10, 2);

function show_custom_columns($column, $post_id) {
	switch ($column) {
		case 'custom_image':
			$image = get_field('image', $post_id);
			if ($image && is_array($image)) {
				echo '<img src="' . esc_url($image['sizes']['thumbnail'] ?? $image['url']) . '" style="max-width:120px;max-height:90px;">';
			} else {
				echo '—';
			}
			break;
		case 'custom_text':
			$text = get_field('text', $post_id);
			echo $text ? esc_html($text) : '—';
			break;
		case 'custom_tax':

			break;
	}
}



/*------------------------------------------------
  3. テーマ・投稿タイプ・タクソノミー
------------------------------------------------*/
add_action('init', function () {
	global $custompostarray, $feedpost;

	$feedpost = [];

	foreach ($custompostarray as $cpt) {
		// 1. カスタム投稿タイプ登録
		register_post_type($cpt['slug'], [
			'label' => $cpt['name'],
			'public' => true,
			'menu_position' => 5,
			'menu_icon' => 'dashicons-admin-page',
			'supports' => ['title', 'editor', 'thumbnail', 'revisions'],
			'has_archive' => true,
			'show_in_rest' => true,
			'rest_base' => $cpt['slug'],
		]);

		// 2. フィード用リスト
		$feedpost[] = $cpt['slug'];

		// 3. タクソノミー登録
		if (!empty($cpt['taxonomy'])) {
			foreach ($cpt['taxonomy'] as $tax) {
				register_taxonomy(
					$cpt['slug'] . '_' . $tax['slug'],
					$cpt['slug'],
					[
						'hierarchical' => $tax["category"],
						'label' => $tax["name"],
						'show_ui' => true,
						'show_admin_column' => true,
						'query_var' => true,
						'show_in_rest' => true,
					]
				);
			}
		}

		// 4. 管理画面カラム追加
		add_filter("manage_edit-{$cpt['slug']}_columns", function ($columns) {
			$position = array_search('title', array_keys($columns));
			if ($position === false) $position = 0;

			$new_columns = [
				'custom_image' => '画像',
				'custom_text'  => 'テキスト'
			];

			$columns_before = array_slice($columns, 0, $position + 1, true);
			$columns_after  = array_slice($columns, $position + 1, null, true);
			$columns = $columns_before + $new_columns + $columns_after;
			return $columns;
		});
		add_action("manage_{$cpt['slug']}_posts_custom_column", 'show_custom_columns', 10, 2);

		// 5. ACFオプションサブページも追加（ACF Proのみ！）
		if (function_exists('acf_add_options_sub_page')) {
			acf_add_options_sub_page([
				'page_title'  => $cpt['name'] . '一覧',
				'menu_title'  => $cpt['name'] . '一覧',
				'parent_slug' => 'edit.php?post_type=' . $cpt['slug'],
				'menu_slug'   => $cpt['slug'] . '_archive',
				'post_id'    =>  $cpt['slug'] . '_archive',
				'capability'  => 'edit_posts'
			]);
		}
	}
});

// REST APIで全タクソノミー情報を追加
add_action('rest_api_init', function () {
	$post_types = get_post_types(['public' => true], 'names');

	foreach ($post_types as $post_type) {
		register_rest_field($post_type, 'terms', [
			'get_callback' => function ($data) {
				$post = get_post($data['id']);
				if (!$post) return [];
				$taxonomies = get_object_taxonomies($post->post_type, 'objects');
				$terms = [];
				foreach ($taxonomies as $slug => $taxonomy) {
					$terms[$taxonomy->name] = [
						'name'  => $taxonomy->label,
						'value' => get_the_terms($post->ID, $slug)
					];
				}
				return $terms;
			},
			'update_callback' => null,
			'schema' => null,
		]);
	}
});

// RSSフィード
add_filter('request', function ($vars) {
	global $feedpost;
	if (isset($vars['feed']) && !isset($vars['post_type'])) {
		$vars['post_type'] = $feedpost;
	}
	return $vars;
});
remove_filter('do_feed_rss2', 'do_feed_rss2', 10); // 元のフィードテンプレ削除
add_action('do_feed_rss2', function () {
	$template_file = '/feed-rss2.php';
	load_template(get_template_directory() . $template_file);
}, 10);


/*------------------------------------------------
  カスタム投稿タイプごとのタクソノミーを事前取得 → ${cpt}_value に格納
  例）$news_value['category'] = [ ['id'=>...,'name'=>...], ... ]
------------------------------------------------*/
if (!function_exists('normalize_tax_key')) {
	function normalize_tax_key($tax_slug, $post_type) {
		// 例: news_category → category に正規化（CPTプレフィックスを外す）
		$prefix = $post_type . '_';
		return (strpos($tax_slug, $prefix) === 0) ? substr($tax_slug, strlen($prefix)) : $tax_slug;
	}
}

if (!function_exists('term_to_array')) {
	function term_to_array($t) {
		// 必要に応じてここで ACF も拾える： $acf = function_exists('get_fields') ? get_fields($t) : [];
		return [
			'id'          => (int)$t->term_id,
			'name'        => $t->name,
			'slug'        => $t->slug,
			'taxonomy'    => $t->taxonomy,
			'count'       => (int)$t->count,
			'parent'      => (int)$t->parent,
			'description' => $t->description,
			'link'        => get_term_link($t),
			// 'acf'      => $acf ?? null, // ←必要なら展開
		];
	}
}

if (!function_exists('get_all_terms_for_cpt')) {
	function get_all_terms_for_cpt($post_type, $args = []) {
		$default_args = ['hide_empty' => true];
		$args = array_merge($default_args, $args);

		$result = [];
		$tax_objects = get_object_taxonomies($post_type, 'objects'); // ['news_category'=>object,...]
		foreach ($tax_objects as $tax_slug => $tax_obj) {
			$terms = get_terms([
				'taxonomy'   => $tax_slug,
				'hide_empty' => $args['hide_empty'],
			]);
			if (!is_wp_error($terms)) {
				$result[$tax_slug] = array_map('term_to_array', $terms);
			}
		}
		return $result; // ['news_category'=> [termArray,...], ...]
	}
}

/**
 * CPT/Tax 登録後にキャッシュを作る
 * init の遅い優先度（99）で、${cpt}_value をグローバルに用意
 */
add_action('init', function () {
	// ここでは $custompostarray を優先（なければ public CPT 全部）
	$post_types = [];
	if (!empty($GLOBALS['custompostarray']) && is_array($GLOBALS['custompostarray'])) {
		foreach ($GLOBALS['custompostarray'] as $cpt) {
			if (!empty($cpt['slug'])) $post_types[] = $cpt['slug'];
		}
	}
	if (empty($post_types)) {
		$post_types = get_post_types(['public' => true, '_builtin' => false], 'names');
	}

	foreach ($post_types as $pt) {
		// ['news_category'=>[...], 'news_tag'=>[...]]
		$all = get_all_terms_for_cpt($pt, ['hide_empty' => true]);

		// 正規化キーで詰め直す（news_category → category）
		$value = [];
		foreach ($all as $tax_slug => $items) {
			$key = normalize_tax_key($tax_slug, $pt);
			$value[$key] = $items; // [ ['id'=>..,'name'=>..,'link'=>..], ... ]
		}

		// 例: $news_value, $product_value として置く
		$GLOBALS[$pt . '_value'] = $value;
	}
}, 99);
/*------------------------------------------------
  4. ACFグループ登録
------------------------------------------------*/
$acfvalues['setting'] = array(
	'name' => 'setting',
	'title' => '共通設定',
	'field' => array(
		array(
			'type' => 'group',
			'label' => 'メタ情報',
			'name' => 'meta',
			'layout' => 'row',
			'sub_fields' => array(
				array(
					'type' => 'text',
					'label' => 'サイトタイトル',
					'name' => 'title',
				),
				array(
					'type' => 'textarea',
					'label' => 'ディスクリプション',
					'name' => 'description',
					'instructions' => '130字程度',
					'rows' => 4,
				),
				array(
					'type' => 'image',
					'label' => 'OGP画像',
					'name' => 'ogp',
					'instructions' => '1200px × 630pxにリサイズしてください。',
					'size' => 'thumbnail',
					'return_format' => 'url',
				),
				array(
					'type' => 'group',
					'label' => 'Google Map',
					'name' => 'map',
					'layout' => 'row',
					'sub_fields' => array(
						array(
							'type' => 'text',
							'label' => 'API',
							'name' => 'api',
							'instructions' => '<a href="https://nendeb.com/276" target="_blank">API取得方法こちら</a>',
						),
						array(
							'type' => 'group',
							'label' => '緯度経度',
							'name' => 'latlng',
							'layout' => 'table',
							'sub_fields' => array(
								array(
									'type' => 'text',
									'label' => '緯度',
									'name' => 'lat',
								),
								array(
									'type' => 'text',
									'label' => '経度',
									'name' => 'lng',
								),
							),
						),
						array(
							'type' => 'link',
							'label' => 'Googlemapリンク',
							'name' => 'link',
						),
					),
				),
				array(
					'type' => 'group',
					'label' => 'Google Analytics',
					'name' => 'analytics',
					'layout' => 'row',
					'sub_fields' => array(
						array(
							'type' => 'true_false',
							'label' => 'どちらを使いますか？',
							'name' => 'ga_or_gtm',
							'ui_on_text' => 'Tag Manager',
							'ui_off_text' => 'Analytics',
						),
						array(
							'type' => 'text',
							'label' => 'Google TagManager',
							'name' => 'gtm',
							'instructions' => 'UTMから始まるアカウントを入力(タグマネージャー画面でアナリティクスの紐付けを行なってください。)',
							'conditional_logic' => array(
								array(
									array(
										'field' => 'ga_or_gtm',
										'operator' => '==',
										'value' => '1',
									),
								),
							),
						),
						array(
							'type' => 'text',
							'label' => 'GoogleAnalytics',
							'name' => 'ga',
							'conditional_logic' => array(
								array(
									array(
										'field' => 'ga_or_gtm',
										'operator' => '!=',
										'value' => '1',
									),
								),
							),
						),
					),
				),
			),
		),
		array(
			'type' => 'group',
			'label' => 'お客さま情報',
			'name' => 'siteinfo',
			'layout' => 'block',
			'sub_fields' => array(
				array(
					'type' => 'text',
					'label' => '会社名',
					'name' => 'company',
					'instructions' => '他ページで[company]で入力するとこの値が反映されます'
				),
				array(
					'type' => 'text',
					'label' => '電話番号',
					'name' => 'tel',
					'instructions' => '他ページで[tel]で入力するとこの値が反映されます'
				),
				array(
					'type' => 'email',
					'label' => 'お問い合わせ先メールアドレス',
					'name' => 'email',
					'instructions' => '他ページで[email]で入力するとこの値が反映されます'
				),
				array(
					'type' => 'text',
					'label' => '郵便番号',
					'name' => 'zip',
					'instructions' => '他ページで[zip]で入力するとこの値が反映されます'
				),
				array(
					'type' => 'text',
					'label' => '住所',
					'name' => 'add',
					'instructions' => '他ページで[add]で入力するとこの値が反映されます'
				),
				array(
					'type' => 'text',
					'label' => '建物名',
					'name' => 'buil',
					'instructions' => '他ページで[buil]で入力するとこの値が反映されます'
				),
				array(
					'type' => 'text',
					'label' => 'コピーライト',
					'name' => 'copyright',
				),
			),
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'initsetting',
			),
		),
	),
);
$pagesetting_locations = [
	[
		[
			'param' => 'post_type',
			'operator' => '==',
			'value' => 'page',
		],
		[
			'param' => 'page',
			'operator' => '!=',
			'value' => $fixedpage_arr['top']['id'],
		],
	]
];
foreach ($custompostarray as $cpt) {
	$pagesetting_locations[] = [
		[
			'param' => 'post_type',
			'operator' => '==',
			'value' => $cpt['slug'],
		]
	];
}
$pagesetting_locations[] = [
	[
		'param' => 'options_page',
		'operator' => '!=',
		'value' => 'initsetting',
	]
];

$acfvalues['pagesetting_main'] = array(
	'name' => 'pagesetting_main',
	'title' => 'ページ設定（メイン）',
	'field' => [
		[
			'type' => 'image',
			'label' => '代表画像(必須)',
			'name' => 'image',
			'size' => 'thumbnail',
		],
		[
			'type' => 'textarea',
			'label' => 'テキスト',
			'name' => 'text',
		]
	],
	'location' => $pagesetting_locations,
	'option' => [
		'position' => 'acf_after_title'  // ← これで「タイトル直下」に表示される
	],
);
$acfvalues['pagesetting_side'] = array(
	'name' => 'pagesetting_side',
	'title' => 'ページ設定',
	'field' => array(
		array(
			'type' => 'group',
			'label' => 'meta設定',
			'name' => 'meta',
			'layout' => 'block',
			'sub_fields' => array(
				array(
					'type' => 'image',
					'label' => 'OGP画像',
					'name' => 'ogp',
					'size' => 'thumbnail',
					'instructions' => '設定がなければアイキャッチ画像が表示',
				),
				array(
					'type' => 'textarea',
					'label' => 'ディスクリプション',
					'name' => 'description',
				),
			),
		),
		array(
			'type' => 'group',
			'label' => '一覧での表示',
			'name' => 'thumbnail',
			'layout' => 'block',
			'sub_fields' => array(
				array(
					'type' => 'image',
					'label' => '画像',
					'name' => 'image',
					'size' => 'thumbnail',
					'instructions' => '設定がなければアイキャッチ画像が表示',
				),
				array(
					'type' => 'textarea',
					'label' => 'テキスト',
					'name' => 'text',
				),
			),
		),
	),
	'location' => $pagesetting_locations,
	'option' => array(
		'position' => 'side',
	),
);
function setAcfRoop($fieldvalues, $page_key) {
	$field = array();
	if (is_array($fieldvalues)) {
		foreach ($fieldvalues as $fieldno => $fieldvalue) {
			$type = $fieldvalue['type'];
			$fieldvalue['key'] = $page_key . '_' . $fieldvalue['name'];
			if ($type == 'group' || $type == 'repeater') {
				$fieldvalue['sub_fields'] = setAcfRoop($fieldvalue['sub_fields'], $fieldvalue['key']);
			} elseif ($type == 'flexible_content') {
				$fieldvalue['layouts'] = array(); // ★ここで初期化
				foreach ($fieldvalue['sub_fields'] as $layoutkey => $layout) {
					$layout['key'] = $fieldvalue['key'] . '_' . $layoutkey;
					$layout['sub_fields'] = setAcfRoop($layout['sub_fields'], $layout['key']);
					$fieldvalue['layouts'][] = $layout;
				}
			}
			$constArray = array(
				'key' => 'field_' . $fieldvalue['key'],
				'label' => $fieldvalue['label'],
				'name' => $fieldvalue['name'],
				'type' => $fieldvalue['type'],
				'instructions' => (isset($fieldvalue['instructions'])) ? $fieldvalue['instructions'] : '',
				'required' => 0,
				'conditional_logic' => (isset($fieldvalue['conditional_logic'])) ? $fieldvalue['conditional_logic'] : 0,
				'wrapper' => array(
					'width' => (isset($fieldvalue['width'])) ? $fieldvalue['width'] : '',
					'class' => '',
					'id' => '',
				),
			);
			$acfFieldSetting = array(
				'text' => array(
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
					'readonly' => 0,
					'disabled' => 0,
				),
				'textarea' => array(
					'default_value' => '',
					'placeholder' => '',
					'rows' => 4,
					'maxlength' => '',
					'new_lines' => 'br',
				),
				'number' => array(
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
					'readonly' => 0,
					'disabled' => 0,
					'min' => '',
					'max' => '',
					'step' => '',
				),
				'image' => array(
					'return_format' => 'array',
					'preview_size' => 'medium',
					'library' => 'all',
					'min_width' => 0,
					'min_height' => 0,
					'min_size' => 0,
					'max_width' => 0,
					'max_height' => 0,
					'max_size' => 0,
					'mime_types' => '',
				),
				'file' => array(
					'return_format' => 'array',
					'preview_size' => 'medium',
					'library' => 'all',
					'min_size' => 0,
					'max_size' => 0,
					'mime_types' => '',
				),
				'gallery' => array(
					'return_format' => 'array',
					'preview_size' => 'medium',
					'library' => 'all',
					'min' => 0,
					'max' => 0,
					'min_width' => 0,
					'min_height' => 0,
					'min_size' => 0,
					'max_width' => 0,
					'max_height' => 0,
					'max_size' => 0,
					'mime_types' => '',
					'insert' => 'append',
				),
				'link' => array(
					'type' => 'link',
					'return_format' => 'array',
				),
				'true_false' => array(
					'message' => '',
					'default_value' => 0,
					'ui' => 1,
					'ui_on_text' => 'はい',
					'ui_off_text' => 'いいえ',
				),
				'select' => array(
					'return_format' => 'array',
					'default_value' => '',
					'choices' => '',
					'allow_null' => 0,
					'multiple' => 0,
					'ui' => 0,
					'ajax' => 0,
					'placeholder' => '',
				),
				'checkbox' => array(
					'return_format' => 'array',
					'default_value' => '',
					'choices' => '',
					'layout' => 'horizontal',
					'allow_custom' => false,
					'save_custom' => false,
					'toggle' => true,
				),
				'radio' => array(
					'return_format' => 'array',
					'default_value' => '',
					'choices' => '',
					'layout' => 'horizontal',
					'save_other_choice' => 0,
					'other_choice' => 0,
				),
				'markdown' => array(
					'autogrow' => 1,
					'editor-theme' => 'dark',
					'preview-theme' => 'github',
					'syntax-highlight' => 0,
					'syntax-theme' => 'monokai_sublime',
					'media-upload' => 0,
					'tab-function' => 0,
				),
				'table' => array(
					'use_header' => 0,
					'use_caption' => 2,
				),
				'group' => array(
					'layout' => (!empty($fieldvalue['layout'])) ? $fieldvalue["layout"] : "",
					'sub_fields' => (!empty($fieldvalue['sub_fields'])) ? $fieldvalue["sub_fields"] : "",
				),
				'repeater' => array(
					'layout' => 'block',
					'collapsed' => '',
					'sub_fields' => (!empty($fieldvalue['sub_fields'])) ? $fieldvalue["sub_fields"] : "",
					'button_label' => 'リストを追加',
					'min' => 0,
					'max' => '',
				),
				'flexible_content' => array(
					'layouts' => (!empty($fieldvalue['layouts'])) ? $fieldvalue["layouts"] : "",
					'button_label' => 'コンテンツを追加',
					'min' => 0,
					'max' =>  '',
				),
				'relationship' => array(
					'post_type' => (!empty($fieldvalue['post_type'])) ? $fieldvalue["post_type"] : "",
					'taxonomy' => '',
					'filters' => array('search', 'post_type', 'taxonomy'),
					'elements' => array(),
					'min' => 0,
					'max' => '',
					'return_format' => 'id',
				),
				'post_object' => array(
					'post_type' => (!empty($fieldvalue['post_type'])) ? $fieldvalue["post_type"] : "",
					'taxonomy' => '',
					'allow_null' => 0,
					'multiple' => 0,
					'ui' => 1,
					'return_format' => 'id',
				),
				'button_group' => array(
					'post_type' => (!empty($fieldvalue['post_type'])) ? $fieldvalue["post_type"] : "",
					'choices' => '',
					'default_value' => '',
					'return_format' => 'value',
					'allow_null' => 0,
					'layout' => 'horizontal',
				),
				'color_picker' => array(
					'enable_opacity' => 0,
					'return_format' => 'string',
				),
				'message' => array(
					'message' => (!empty($fieldvalue['message'])) ? $fieldvalue["message"] : "",
					'esc_html' => 0,
					'new_lines' => 'br',
				),
				'tab' => array(
					'placement' => 'top',
					'endpoint' => 0,
					'parent_repeater' => '',
				),
			);
			if (!empty($acfFieldSetting[$type])) {
				foreach ($acfFieldSetting[$type] as $key => $value) {
					$constArray[$key] = (isset($fieldvalue[$key])) ? $fieldvalue[$key] : $value;
				}
			}
			$field[] = $constArray;
		}
	}
	return $field;
}
$acf_fields = array();
foreach ($acfvalues as $pagevalue) {
	$page_field = array(
		'key' => 'group_' . $pagevalue['name'],
		'title' =>  $pagevalue['title'],
		'fields' => $pagevalue['field'],
		'location' => $pagevalue['location'],
		'menu_order' => 6,
		'position' => (!empty($pagevalue['position'])) ? $pagevalue['position'] : 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => array(
			// 0 => 'permalink',
			1 => 'the_content',
			2 => 'excerpt',
			3 => 'discussion',
			4 => 'comments',
			5 => 'revisions',
			6 => 'slug',
			7 => 'author',
			8 => 'format',
			9 => 'page_attributes',
			10 => 'featured_image',
			11 => 'categories',
			12 => 'tags',
			13 => 'send-trackbacks',
		),
		'active' => true,
		'description' => '',
	);
	if (!empty($pagevalue['option'])) {
		foreach ($pagevalue['option'] as $key => $value) {
			$page_field[$key] = $value;
		}
	}
	$page_key = $pagevalue['name'];
	$page_field['fields'] = setAcfRoop($page_field['fields'], $page_key);
	$acf_fields[] = $page_field;
}
if (function_exists('acf_add_local_field_group')) :
	foreach ($acf_fields as $acf_field) {
		acf_add_local_field_group($acf_field);
	}
endif;


/*------------------------------------------------
  5. ショートコード
------------------------------------------------*/
$shortcodes = [
	'hurl'      => function () {
		return home_url('/');
	},
	'company'   => function () {
		return get_field('siteinfo', 'setting')['company'];
	},
	'email'     => function () {
		return get_field('siteinfo', 'setting')['email'];
	},
	'tel'       => function () {
		return get_field('siteinfo', 'setting')['tel'];
	},
	'tellink'   => function () {
		$tel = get_field('siteinfo', 'setting')['tel'];
		return '<a href="tel:' . str_replace("-", "", $tel) . '" class="tel">' . $tel . '</a>';
	},
	'maplink'   => function () {
		$link = get_field('meta', 'setting')['map']['link']['url'];
		return '<a href="' . $link . '" target="_blank" class="map">MAP</a>';
	},
	'zip'       => function () {
		return get_field('siteinfo', 'setting')['zip'];
	},
	'add'       => function () {
		return get_field('siteinfo', 'setting')['add'];
	},
	'buil'      => function () {
		return get_field('siteinfo', 'setting')['buil'];
	},
	'add_notag' => function () {
		return strip_tags(get_field('siteinfo', 'setting')['add']);
	},
];

foreach ($shortcodes as $name => $fn) {
	add_shortcode($name, $fn);
}
add_filter('acf/format_value/type=textarea', 'do_shortcode');
add_filter('acf/format_value/type=text', 'do_shortcode');


/*------------------------------------------------
  6. 多言語
------------------------------------------------*/
// --- 現在言語コードを常に "ja" / "en" などの string で返す ---
if (!function_exists('get_current_lang_code')) {
	function get_current_lang_code() {
		// 1) まずは WordPress の現在ロケール（Bogoがここを切り替える）
		$locale = function_exists('determine_locale') ? determine_locale() : get_locale();
		$code = is_string($locale) ? strtolower(substr($locale, 0, 2)) : 'ja'; // en_US -> en

		// 2) シングル（投稿/固定）なら Bogo で厳密化
		if (function_exists('bogo_get_language') && function_exists('is_singular') && is_singular()) {
			$qid = function_exists('get_queried_object_id') ? get_queried_object_id() : 0;
			if ($qid) {
				$blang = bogo_get_language($qid); // "ja" / "en" が返る想定
				if (is_string($blang) && $blang !== '') {
					return $blang;
				}
			}
		}

		// 3) それ以外（アーカイブ、フロントの早期フック、管理画面など）は code を返す
		return $code ?: 'ja';
	}
}

// --- 言語別オプションを取得（なければ共通にフォールバック） ---
if (!function_exists('get_lang_option_fields')) {
	function get_lang_option_fields($base_post_id, $use_fallback = true) {
		$lang = get_current_lang_code();           // 'ja' or 'en'
		$pid_lang = "{$base_post_id}_{$lang}";     // 例: news_archive_en
		$fields = function_exists('get_fields') ? get_fields($pid_lang) : null;

		if (!$fields && $use_fallback) {
			$fields = function_exists('get_fields') ? get_fields($base_post_id) : null;
		}
		return is_array($fields) ? $fields : [];
	}
}

/*------------------------------------------------
  6. API・その他
------------------------------------------------*/
// パスワード保護テキストカスタム
function my_password_form() {
	return
		'<div class="member_pas">
	<p>閲覧するためには、パスワードが必要になります。閲覧するには以下にパスワードを入力してください。<p>
    <form class="post_password" action="' . home_url() . '/wp-login.php?action=postpass" method="post">
    <input name="post_password" type="password" size="24"/>
    <input type="submit" name="Submit" " value="' . esc_attr__("パスワード") . '" />
    </form>
　　　</div>
	';
}
add_filter('the_password_form', 'my_password_form');


// カラーミーショップ
define("COLORME_OAUTH2_TOKEN", 'APIキー');
$request_options = array(
	'http' => array(
		'method' => 'GET',
		'header' => "Authorization: Bearer " . COLORME_OAUTH2_TOKEN . "\r\n"
	)
);
define("CONTEXT", stream_context_create($request_options));

function getItem($id) {
	$geturl = 'https://api.shop-pro.jp/v1/products.json?ids=' . $id . '&limit=50';
	$response_body = file_get_contents($geturl, false, CONTEXT);
	$productjson = mb_convert_encoding($response_body, 'UTF-8', 'auto');
	$product = json_decode($productjson, true);
	return $product["products"];
}

function getPrice($thisproduct) {
	$thisprice = number_format($thisproduct["sales_price_including_tax"]);
	$pricelist = array();
	foreach ((array)$thisproduct['variants'] as $thisvalue) {
		$opt_price = $thisvalue['option_price_including_tax'];
		if (isset($opt_price)) {
			if (!in_array($opt_price, $pricelist)) {
				array_push($pricelist, $opt_price);
			}
		}
	}
	if (!empty($pricelist)) {
		if (count($pricelist) > 1) {
			$thisprice = number_format(min($pricelist)) . " ~ ¥" . number_format(max($pricelist));
		}
	}
	return '¥' . $thisprice;
}


function getSaleStatus($thisproduct) {
	// trueが売り切れ
	$status = true;
	$status = ($thisproduct["stock_managed"] == true && $thisproduct["stocks"] == 0) ? true : false;
	return $status;
}



// ───────────────────────────────────────────────
// お問い合わせフォーム設定をWPの値で上書きする
// ───────────────────────────────────────────────
if (!function_exists('wp_override_form_setting')) {
	function wp_override_form_setting(array $form_setting): array {
		// 1) 送信先など（ACF「設定」オプションから）
		$siteinfo = function_exists('get_field') ? (get_field('siteinfo', 'setting') ?: []) : [];
		$client_name = $siteinfo['company'] ?? ($form_setting['userMail']['fromName'] ?? '');
		$email       = $siteinfo['email']   ?? ($form_setting['userMail']['to'] ?? '');

		// userMail
		$form_setting['userMail']['to']       = $email ?: ($form_setting['userMail']['to'] ?? '');
		$form_setting['userMail']['from']     = $email ?: ($form_setting['userMail']['from'] ?? '');
		$form_setting['userMail']['fromName'] = $client_name ?: ($form_setting['userMail']['fromName'] ?? '');
		if (empty($form_setting['userMail']['subject'])) {
			$form_setting['userMail']['subject'] = '[' . $client_name . ']お問い合わせありがとうございました';
		}

		// adminMail
		$form_setting['adminMail']['to']   = $email ?: ($form_setting['adminMail']['to'] ?? '');
		$form_setting['adminMail']['from'] = $email ?: ($form_setting['adminMail']['from'] ?? '');
		if (empty($form_setting['adminMail']['subject'])) {
			$form_setting['adminMail']['subject'] = 'ホームページのお問い合わせがありました';
		}

		// 2) 画面遷移URL（固定ページの実URLで補正）
		if (function_exists('get_page_by_path') && function_exists('get_permalink')) {
			// contact
			$p_contact = get_page_by_path('contact', OBJECT, 'page');

			// contact/confirm
			$p_confirm = get_page_by_path('contact/confirm', OBJECT, 'page');
			if ($p_confirm instanceof WP_Post) {
				$form_setting['confirm'] = trailingslashit(get_permalink($p_confirm->ID));
			} elseif ($p_contact instanceof WP_Post) {
				// 子ページが未発行なら /contact/confirm/ を補完
				$form_setting['confirm'] = trailingslashit(get_permalink($p_contact->ID)) . 'confirm/';
			}

			// contact/thanks
			$p_thanks = get_page_by_path('contact/thanks', OBJECT, 'page');
			if ($p_thanks instanceof WP_Post) {
				$form_setting['thanks'] = trailingslashit(get_permalink($p_thanks->ID));
			} elseif ($p_contact instanceof WP_Post) {
				$form_setting['thanks'] = trailingslashit(get_permalink($p_contact->ID)) . 'thanks/';
			}
		}

		return $form_setting;
	}
}


/*------------------------------------------------
  インポーター
------------------------------------------------*/
if ( is_admin() ) {
	require_once __DIR__ . '/inc/admin-import.php';
}
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once __DIR__ . '/inc/cli-import.php';
}
