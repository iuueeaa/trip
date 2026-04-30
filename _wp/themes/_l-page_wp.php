<?php
/*------------------------------------------------
1. パス・グローバル変数
	------------------------------------------------*/
// functions.phpに設定


// --- ここでWordPress専用：投稿の子ページ追加 ---


// $menu_listにある固定ページのURLを上書き
// foreach ($menu_list as $menuslug => $menu) {
// 	$page_ID = get_page_by_path($menuslug);
// 	if ($page_ID) {
// 		$menu_list[$menuslug]['url'] = get_the_permalink($page_ID->ID);
// 	}
// }

// 投稿をナビに出したい時の設定。
// foreach ($addChildPage as $addpagekey => $display) {
// 	$childargs = array(
// 		'post_type' => $addpagekey,
// 		'posts_per_page' => '-1',
// 		'post_status' => 'publish',
// 		'has_password' => false,
// 	);
// 	$the_query = new WP_Query($childargs);
// 	if ($the_query->have_posts()) {
// 		while ($the_query->have_posts()) {
// 			$the_query->the_post();
// 			$this_post = get_fields();
// 			if (empty(get_field("title"))) {
// 				$this_post['title'] = array('main' => get_the_title());
// 			}
// 			$menu_list[$addpagekey]['children'][] = array(
// 				'title' =>	$this_post['title'],
// 				'url' => get_the_permalink(),
// 				'display' => $display,
// 				'class' => '',
// 				'icon' => '',
// 			);
// 		}
// 	}
// 	wp_reset_query();
// }


/*------------------------------------------------
3. ページvalueの自動展開
	------------------------------------------------*/
global $this_page_value;
$this_page_value = loadPageValue();
$page_class      = $this_page_value["class"] ?? "";
$bodyclass       = $page_class ? $page_class . ' is-ready' : 'is-ready';


/*------------------------------------------------
3-B. 共通変数のACFの値を取得
	------------------------------------------------*/
$siteinfo = get_field('siteinfo', 'setting');
$meta     = get_field('meta', 'setting');
$common_ogp = $meta['ogp'] ?? '';
$site_title = $meta['title'] ?? '';
$site_description = strip_tags($meta['description'] ?? '');
$twitteraccount = $meta['twitter'] ?? '';


$client_name = $siteinfo['company'] ?? '';
$copyright = $siteinfo['copyright'] ?? '';
$project_name = $site_title . "サイト制作";
$analytics = $meta['analytics'] ?? [];
$ga = !$analytics['ga_or_gtm'] ? ($analytics['ga'] ?? '') : '';
$gtm = $analytics['ga_or_gtm'] ? ($analytics['gtm'] ?? '') : '';


$tel = $siteinfo['tel'] ?? '';
$email = $siteinfo['email'] ?? '';
$zip = $siteinfo['zip'] ?? '';
$add = $siteinfo['add'] ?? '';
$buil = $siteinfo['buil'] ?? '';
$open = $siteinfo['open'] ?? '';

$latlng = $meta['map']['latlng'] ?? [];
$lat = $latlng['lat'] ?? '';
$lng = $latlng['lng'] ?? '';

$map_link = $meta['map']['link']['url'] ?? '';
$map_api = $meta['map']['api'] ?? '';


$page_common['cta'] = get_field('cta', 'setting');
$page_common['note_aside'] = get_field('note_aside', 'setting');
if (!empty($this_page_value['setting'])) {
	$this_page_value['setting'] = wp_override_form_setting($this_page_value['setting']);
}


/*------------------------------------------------
4. metaの変数設定
	------------------------------------------------*/
$page_title = strip_tags($this_page_value["meta"]["title"] ?? $this_page_value["title"]["main"] ?? "");
$page_description = strip_tags($this_page_value["meta"]["description"] ?? "");
$page_ogimage = $this_page_value["meta"]["ogp"]["sizes"]["large"]
	?? $this_page_value["image"]["sizes"]["large"]
	?? "";


$meta = [
	'title'           => $page_title ? "$page_title | $site_title" : $site_title,
	'description'     => $page_description ?: $site_description,
	'url'             => $site_url . $_SERVER["REQUEST_URI"],
	'type'            => "website",
	'ogimage'         => $page_ogimage ?: $common_ogp,
	'site_title'      => $site_title,
	'twittercard'     => $twittercard ?? 'summary_large_image', // ここ
	'twitteraccount'  => $twitteraccount ?? '',
	'telephone'       => $telephone ?? false,
	'viewport'        => $viewport ?? false,
	'theme_color'     => $theme_color ?? "",
	'favicon'         => $image_path . "common/favicon.ico",
	'apple_icon'      => $image_path . "common/apple-touch-icon.webp",
	'googlefont'      => $googlefont ?? [], // ここ
	'lang'            => $lang,
];


if (!empty($confirmFlg) && $confirmFlg == 1) {
	include($inc_path . "meta/mail.php");
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
	<?php include($inc_path . "meta/meta.php"); ?>
	<?php include($inc_path . "meta/tag_head.php"); ?>
	<?php //include("schema.php");
	?>
	<?php wp_head(); ?>
</head>

<body class="<?php echo $bodyclass; ?>">
	<?php include($inc_path . "meta/tag_body.php"); ?>
	<div id="wrapAll">
		<?php include($inc_path . "_l-header.php"); ?>
		<?php include($inc_path . "_l-main.php"); ?>
		<?php include($inc_path . "_l-foot.php"); ?>
	</div>
	<?php include($inc_path . "_l-script.php"); ?>
	<?php wp_footer(); ?>
</body>

</html>
