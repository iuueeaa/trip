<?php

function SetboxClass($class) {
	if (is_array($class)) {
		return implode(' ', $class);
	} else {
		return $class;
	}
}

if (!function_exists('safe_url')) {
	function safe_url($url) {
		if (function_exists('esc_url')) {
			return esc_url($url);
		}
		return htmlspecialchars((string)$url, ENT_QUOTES, 'UTF-8');
	}
}

if (!function_exists('safe_html')) {
	function safe_html($text) {
		if (function_exists('esc_html')) {
			return esc_html($text);
		}
		return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
	}
}

if (!function_exists('safe_attr')) {
	function safe_attr($text) {
		if (function_exists('esc_attr')) {
			return esc_attr($text);
		}
		return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
	}
}

function SetBoxLink($link) {
	$array = array();
	if ($link['mode'] == 'file') {
		$array['url'] = $link['file'];
		$array['target'] = '_blank';
	} else {
		$array['url'] = (!empty($link['link']['url'])) ? $link['link']['url'] : '';
		$array['target'] = (!empty($link['link']['target'])) ? $link['link']['target'] : '_self';
		$array['title'] = (!empty($link['link']['title'])) ? $link['link']['title'] : '_self';
	}
	return $array;
}

function setValueTitle($main = "", $sub = "", $icon = "") {
	return array(
		'main' => (!empty($main)) ? $main : "",
		'sub' => (!empty($sub)) ? $sub : "",
		'icon' => (!empty($icon)) ? $icon : "",
	);
}

function setValueImage($imageSrc = "", $imageTitle = "") {
	global $image_path;
	return array(
		'title' => (!empty($imageTitle)) ? $imageTitle : "画像タイトル",
		'sizes' => array(
			'large' => (!empty($imageSrc)) ? $imageSrc : $image_path . '_dummy/pic-dummy.jpg',
			'medium' => (!empty($imageSrc)) ? $imageSrc : $image_path . '_dummy/pic-dummy.jpg',
			'thumbnail' => (!empty($imageSrc)) ? $imageSrc : $image_path . '_dummy/pic-dummy.jpg',
		),
	);
}

function setValueLink($url = "", $title = "", $target = "") {
	return array(
		'mode' => "link",
		'link' => array(
			'url' => (!empty($url)) ? $url : "",
			'title' => (!empty($title)) ? $title : "VIEW DETAIL",
			'target' => (!empty($target)) ? $target : "_self",
		),
	);
}
function setValueVideo($videoSrc = "", $imageSrc = "") {
	return array(
		'video' => $videoSrc,
		'image' => setValueImage($imageSrc),
	);
}
function setValueYoutube($youtubeId = "", $imageSrc = "") {
	return array(
		'id' => $youtubeId,
		'image' => setValueImage($imageSrc),
	);
}
function buildTaxonomyUrl($postType, $taxSlug, $termSlug) {
	global $link_path;
	return $link_path . '/' . $postType . '?' . $taxSlug . '=' . $termSlug;
}
function setValueFile($url = "", $title = "") {
	return array(
		'mode' => "file",
		'filetitile' => (!empty($title)) ? $title : "",
		'file' => (!empty($url)) ? $url : "",
	);
}

function setValueTaxonmy($taxnomy, $term, $array) {
	$taxonomy = array();
	foreach ($array as $key => $value) {
		$arr = [
			'name' => $value[0],
			'slug' => $value[1],
			'taxonomy' => $taxnomy . "_" . $term,
			'term' => $term,
			'id' => $key,
			'parent' => $taxnomy
		];
		// if (!empty($value[2])) {
		// 	$arr->color = $value[2];
		// }
		$taxonomy[] = $arr;
	}
	return $taxonomy;
}

function setValueInput($type = "", $name = "", $label = "", $require = false, $attr = array()) {
	$array = array(
		'type' => $type,
		'name' => $name,
		'label' => $label,
		'req' => $require
	);
	$array = array_merge($array, $attr);
	return $array;
}

/**
 * ページタイトル共通取得
 * $page: ページvalueの配列
 * $type: 'meta', 'nav', 'pankuzu'など
 */
function getPageTitle($page, $type = 'meta') {
	global $setting_title_preference;
	$prefer = $setting_title_preference[$type];
	// 個別指定
	if (!empty($page[$type]['title'])) {
		return $page[$type]['title'];
	}
	// メイン/サブの優先
	if (!empty($page['title'][$prefer])) {
		return $page['title'][$prefer];
	}
	// 逆を補完
	$alt = $prefer === 'main' ? 'sub' : 'main';
	return $page['title'][$alt] ?? '';
}

/**
 * ページvalueのデフォルト設定
 * $p_key: 'top', 'company_history'など
 * $overrides: 上書きするページvalueの配列
 */
function defaultPageValue($p_key, $overrides = array()) {
	global $date_format, $image_path;
	$defaults = array(
		'title'      => setValueTitle('', ''),
		'date'       => date($date_format),
		'class' => 'page-' . $p_key,
		'id'         => 0,
		'slug'       => $p_key,
		'pankuzu' => '',
		'parent' => 'home', // ←親のmenu_listのkey
		'nav' => setPageNav($p_key),
		'section_mode' => $p_key,
		'taxonomy'   => array(),
		// メインの画像・リード（ページごとに明示指定）
		// 'image' => setValueImage($image_path . '_dummy/pic-dummy.jpg', '画像タイトル'),
		// 'text'      => 'この文章はメインのリード文章のダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		//
		// 'meta'       => array(
		// 	// 'ogp'         => setValueImage('', ''),
		// 	'description'=>'この文章はディスクリプションの文章のダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		// ),
		// 'thumbnail'  => array(
		// 	// 'image' => setValueImage('', ''),
		// 	'text'  => 'この文章は一覧表示の時の文章のダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		// ),
	);
	return array_merge($defaults, $overrides);
}

function loadPageValue() {
	global $wpflg, $page_value_name, $sitemapArr; // ★ 追加：$sitemapArr

	if ($wpflg) {
		// 1. アーカイブ
		if (is_archive()) {
			$post_type = get_query_var('post_type') ?: get_post_type() ?: get_post_type(get_queried_object());
			$key = $post_type;
			$page_var = "page_" . $key;
			global $$page_var;
			$value = $$page_var ?? [];
			unset($value['title']); // 静的データのtitleは常に無視
			$value['slug'] = $key;
			$value['post_type'] = $key;
			$value['nav']['url'] = get_post_type_archive_link($key);

			// カスタムフィールド（ACFオプション: {cpt}_archive）
			$value = array_merge($value, get_lang_option_fields($key . '_archive', true));

			if (empty($value['title'])) {
				$value['title'] = ['main' => 'ページのタイトルが入ります'];
			}
			return $value;
		}

		// 2. 投稿ページ（single）
		if (is_single()) {
			global $post;
			$post_type = get_post_type($post);

			// ${posttype}_list の雛形（静的と共通化）
			$list_var = $post_type . '_list';
			global $$list_var;
			$list  = $$list_var ?? [];
			$value = $list[0] ?? [];
			unset($value['title']); // 静的データのtitleは常に無視

			$value['id']        = get_the_ID();
			$value['slug']      = $post->post_name;
			$value['post_type'] = $post_type;
			$value['parent']    = $post_type;
			$value['date']      = get_the_date();
			$value['nav']['url'] = get_permalink();

			// 記事ACF
			$value = array_merge($value, get_fields($post->ID) ?: []);
			if (empty($value['title'])) $value['title'] = ['main' => get_the_title()];

			// タクソノミー（この投稿に付与されたターム）
			$value['taxonomy'] = $value['taxonomy'] ?? [];
			$tax_objects = get_object_taxonomies($post_type, 'objects');
			foreach ($tax_objects as $tax_slug => $tax_obj) {
				$norm  = normalize_tax_key($tax_slug, $post_type);
				$terms = get_the_terms($post->ID, $tax_slug);
				if (!is_wp_error($terms) && !empty($terms)) {
					$value['taxonomy'][$norm] = array_map('term_to_array', $terms);
				}
			}

			return $value;
		}

		// 3. 固定ページ
		if (have_posts()) {
			while (have_posts()) : the_post();
				$post = get_post();

				// 親子スラッグ結合（page/about/company -> 'page_about_company' 風）
				$key = function_exists('get_full_slug') ? get_full_slug($post) : $post->post_name;
				$page_var = "page_" . $key;
				global $$page_var;
				$value = $$page_var ?? [];
				unset($value['title']); // 静的データのtitleは常に無視

				$value['id']        = get_the_ID();
				$value['slug']      = $post->post_name;
				$value['post_type'] = $post->post_type;

				if ($post->post_type === 'page') {
					if ($post->post_name === 'top') {
						$value['parent'] = null;
					} elseif ($post->post_parent) {
						$parent = get_post($post->post_parent);
						$value['parent'] = $parent ? $parent->post_name : 'home';
					} else {
						$value['parent'] = 'home';
					}
				}

				$value['date']      = get_the_date();
				$value['nav']['url'] = get_permalink();

				// ACFマージ
				$value = array_merge($value, get_fields() ?: []);
				if (empty($value['title'])) $value['title'] = ['main' => get_the_title()];
				return $value;
			endwhile;
		}
	}

	// ===== 静的処理 =====
	$var = isset($page_value_name) ? $page_value_name : "page_top";
	global $$var;

	// /topics/detail.php?id=3 などの簡易 single 互換
	if (isset($_GET['id'])) {
		$base     = preg_replace('/^page_/', '', $var); // "page_"を除去
		$list_var = $base . "_list";
		global $$list_var;
		$id = (int)$_GET['id'];                          // ★ 数値キャスト
		if (isset($$list_var) && isset($$list_var[$id])) {
			return $$list_var[$id];
		}
	}

	// archive/pageなど
	return $$var ?? [];
}

function get_full_slug($post) {
	$slugs = [];
	while ($post->post_parent) {
		array_unshift($slugs, $post->post_name);
		$post = get_post($post->post_parent);
	}
	array_unshift($slugs, $post->post_name);
	return implode('_', $slugs);
}

function is_tablet() {
	$useragents = array(
		'iPad'
	);
	$pattern = '/' . implode('|', $useragents) . '/i';
	return preg_match($pattern, $_SERVER['HTTP_USER_AGENT']);
}
function is_mobile() {
	$useragents = array(
		'iPhone', // iPhone
		'iPod', // iPod touch
		'Android', // 1.5+ Android
		'dream', // Pre 1.5 Android
		'CUPCAKE', // 1.5+ Android
		'blackberry9500', // Storm
		'blackberry9530', // Storm
		'blackberry9520', // Storm v2
		'blackberry9550', // Storm v2
		'blackberry9800', // Torch
		'webOS', // Palm Pre Experimental
		'incognito', // Other iPhone browser
		'webmate' // Other iPhone browser
	);
	$pattern = '/' . implode('|', $useragents) . '/i';
	return preg_match($pattern, $_SERVER['HTTP_USER_AGENT']);
}

include($root_path . '/assets/inc/function/post.php');
include($root_path . '/assets/inc/function/wp.php');

$layoutWidth = false;
// --- partsの自動読み込み ---
foreach (glob($root_path . '/assets/inc/function/parts/*.php') as $part) {
	if (basename($part) != "index.php") include $part;
}

/**
 * sitemap.jsonから指定されたpost_typeのtaxonomyリストを取得
 * @param string $postType post_typeのslug（例: 'technology'）
 * @param string $taxSlug taxonomyのslug（例: 'category'）
 * @return array タクソノミーの配列 [['name'=>'...', 'slug'=>'...', 'parent'=>'...'], ...]
 */
function getTaxonomyListArray($postType, $taxSlug) {
	global $wpflg, $sitemapArr, $root_path;

	if ($wpflg) {
		$taxonomy = $postType . '_' . $taxSlug;
		$terms = get_terms([
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		]);
		if (is_wp_error($terms) || empty($terms)) return array();

		// term_id → slug マップ（親解決用）
		$idToSlug = array();
		foreach ($terms as $term) {
			$idToSlug[$term->term_id] = $term->slug;
		}

		$flatList = array();
		foreach ($terms as $index => $term) {
			$parentSlug = ($term->parent && isset($idToSlug[$term->parent]))
				? $idToSlug[$term->parent]
				: null;
			$flatList[$term->slug] = array(
				'name'     => $term->name,
				'slug'     => $term->slug,
				'parent'   => $parentSlug,
				'text'     => $term->description ?? '',
				'name_en'  => (function_exists('get_field') ? get_field('name_en', $term) : null) ?? '',
				'icon'     => (function_exists('get_field') ? get_field('icon', $term) : null) ?? '',
				'taxonomy' => $taxonomy,
				'term'     => $taxSlug,
				'id'       => $index,
				'children' => array(),
			);
		}

		// 階層構造に変換
		$result = array();
		foreach ($flatList as $slug => $item) {
			if (empty($item['parent'])) {
				$result[$slug] = $item;
			} else {
				$parentSlug = $item['parent'];
				if (isset($flatList[$parentSlug])) {
					$flatList[$parentSlug]['children'][$slug] = $item;
				} else {
					$result[$slug] = $item;
				}
			}
		}
		foreach ($result as $slug => $item) {
			$result[$slug] = $flatList[$slug];
		}
		return $result;
	}

	// $sitemapArrがまだロードされていない場合はロード
	if (empty($sitemapArr)) {
		$file = $root_path . '/assets/files/sitemap.json';
		if (file_exists($file)) {
			$sitemapJson = file_get_contents($file);
			$sitemapArr = json_decode($sitemapJson, true);
		} else {
			return array();
		}
	}

	// post_typeに該当するsitemapエントリを探す
	$targetSitemap = null;
	foreach ($sitemapArr as $sitemap) {
		if ($sitemap['slug'] === $postType) {
			$targetSitemap = $sitemap;
			break;
		}
	}

	if (empty($targetSitemap) || empty($targetSitemap['taxonomy'])) {
		return array();
	}

	// 指定されたtaxonomyを探す
	$targetTax = null;
	foreach ($targetSitemap['taxonomy'] as $tax) {
		if ($tax['slug'] === $taxSlug) {
			$targetTax = $tax;
			break;
		}
	}

	if (empty($targetTax) || empty($targetTax['value'])) {
		return array();
	}

	// valueをフラットな配列に変換
	$flatList = array();
	$allSlugs = array();
	foreach ($targetTax['value'] as $item) {
		$allSlugs[] = $item[1];
	}
	foreach ($targetTax['value'] as $index => $item) {
		$parent = null;
		$text = null;
		$name_en = null;
		$icon = null;
		if (isset($item[2])) {
			if (in_array($item[2], $allSlugs)) {
				$parent = $item[2];
				$text = isset($item[3]) ? $item[3] : null;
			} else {
				$text = $item[2];
				$name_en = isset($item[3]) ? $item[3] : null;
				$icon = isset($item[4]) ? $item[4] : null;
			}
		}
		$flatList[$item[1]] = array(
			'name' => $item[0],
			'slug' => $item[1],
			'parent' => $parent,
			'text' => $text,
			'name_en' => $name_en,
			'icon' => $icon,
			'taxonomy' => $postType . '_' . $taxSlug,
			'term' => $taxSlug,
			'id' => $index,
			'children' => array(),
		);
	}

	// 階層構造に変換
	$result = array();
	foreach ($flatList as $slug => $item) {
		if (empty($item['parent'])) {
			// 親がない = トップレベル
			$result[$slug] = $item;
		} else {
			// 親がある = 親のchildrenに追加
			$parentSlug = $item['parent'];
			if (isset($flatList[$parentSlug])) {
				$flatList[$parentSlug]['children'][$slug] = $item;
			} else {
				// 親が見つからない場合はトップレベルに追加
				$result[$slug] = $item;
			}
		}
	}

	// 参照を更新（flatListの変更をresultに反映）
	foreach ($result as $slug => $item) {
		$result[$slug] = $flatList[$slug];
	}

	return $result;
}

// Sitemap.jsonから$custompostarrayを生成
if (!isset($sitemapArr)) {
	$file = $root_path . '/assets/files/sitemap.json';
	// $sitemapJson = mb_convert_encoding(file_get_contents($file), "UTF-8", "ASCII, JIS, UTF-8, EUC-JP, SJIS-WIN");
	$sitemapJson = file_get_contents($file);
	$sitemapArr = json_decode($sitemapJson, true);
}
$custompostarray = array_filter($sitemapArr, function ($value, $index) {
	return $value['post_type'] == 'post';
}, ARRAY_FILTER_USE_BOTH);

foreach ($custompostarray as $thiscpt) {
	$addposttype = array(
		array(
			'param' => 'post_type',
			'operator' => '==',
			'value' => $thiscpt["slug"],
		),
	);
	$acfvalues['pagesetting']['location'][] = $addposttype;
}

if (empty($menu_list)) {
	$menu_list = array();
	foreach ($sitemapArr as $sitemap) {
		$menu_list[$sitemap['slug']] = sitemapToMenuArr($sitemap);
	}
}
function sitemapToMenuArr($sitemap, $parentSlug = "") {
	global $link_path;

	$slug = (!empty($parentSlug)) ? $parentSlug . '/' . $sitemap['slug'] : $sitemap['slug'];
	if (!empty($sitemap['url'])) {
		if (strpos($sitemap['url'], '/') === 0) {
			$pageurl = $link_path . $sitemap['url'];
		} else {
			$pageurl = $sitemap['url'];
		}
	} else {
		$pageurl = $link_path . '/' . $slug . '/';
	}
	$array = array(
		'url' =>		$pageurl,
		'target' => (!empty($sitemap['target'])) ? $sitemap['target'] : "",
		'title' => setValueTitle($sitemap['name'], $sitemap['name_en']),
		'icon' => $sitemap['icon'],
		'class' => $sitemap['class'],
		"display" => $sitemap['display'],

	);

	if ($sitemap['slug'] == "home") {
		$array["url"] = $link_path . "/";
	}

	if (!empty($sitemap['children'])) {
		if (is_array($sitemap['children']) && isset($sitemap['children']['type'])) {
			// 動的生成指定（{type, slug, all}）のため静的 children としてはスキップ
		} else {
			foreach ((array)$sitemap['children'] as $children) {
				if (!empty($children['slug'])) {
					$array['children'][$children['slug']] = sitemapToMenuArr($children, $slug);
				}
			}
		}
	}
	return $array;
}

function setPageNav($p_key) {
	global $menu_list;
	$keys = explode("_", $p_key); // company_history_outline など
	$nav = $menu_list;
	foreach ($keys as $key) {
		if (!empty($nav[$key])) {
			$nav = $nav[$key];
			if (!empty($nav['children'])) $nav = $nav['children'];
		}
	}
	return is_array($nav) ? $nav : [];
}

$spriteXml = $image_path . "icon/sprite.svg";
$iconselect = array();
if (file_exists($spriteXml)) {
	$spriteXmlData = simplexml_load_file($spriteXml);
	foreach ($spriteXmlData as $icon) {
		$iconselect[] = $icon['id'];
	}
}

// function setBoxAddClass($boxSetting)
// {
// 	$addClass = 'p-box__' . $boxSetting['layout'];

// 	// boxに追加するclassの設定
// 	$boxAddClass = array();
// 	$boxClassArray = array('type', 'round', 'boxBorderColor', 'boxColor', 'image', 'imageTextRatio', 'round', 'imagePadding', 'linkFull', 'reverse', 'textAlign');
// 	foreach ($boxClassArray  as  $value) {
// 		if (!empty($boxSetting[$value])) {
// 			if (is_bool($boxSetting[$value]) === true) {
// 				$boxAddClass[] = 'is-' . $value;
// 			} else {
// 				$boxAddClass[] = 'is-' . $value . '__' . $boxSetting[$value];
// 			}
// 		}
// 	}
// 	$addClass .= ' ' . implode(" ", $boxAddClass);
// 	return $addClass;
// }

// function setTitleSetting($layout_num = "default", $pos = "body")
// {
// 	global $textBoxSetting;
// 	$bodyTitleSetting = $textBoxSetting[$pos]['default'];
// 	foreach (array('title', 'h', 'lead', 'class') as $ts) {
// 		$bodyTitleSetting[$ts] = $textBoxSetting[$pos][$layout_num][$ts] ?? $bodyTitleSetting[$ts];
// 	}
// 	return 	$bodyTitleSetting;
// }

function getResponsiveClasses($breakpoint): array
{
	if ($breakpoint === 'tbsp') {
		return ['pc' => 'show_pc', 'sp' => 'show_tbsp'];
	}
	return ['pc' => 'show_pctb', 'sp' => 'show_sp'];
}

function detectLinkIcon(string $url): string
{
	$rules = array(
		'.pdf'          => 'icon-file',
		'tel:'          => 'icon-tel',
		'mailto:'       => 'icon-mail',
		'.mp4'          => 'icon-play',
		'.webp'         => 'icon-image',
		'.gif'          => 'icon-image',
		'youtu.be'      => 'sns-youtube',
		'youtube.com'   => 'sns-youtube',
		'twitter.com'   => 'sns-x',
		'x.com'         => 'sns-x',
		'facebook.com'  => 'sns-facebook',
		'instagram.com' => 'sns-instagram',
	);
	foreach ($rules as $pattern => $icon) {
		if (strpos($url, $pattern) !== false) {
			return $icon;
		}
	}
	return '';
}
