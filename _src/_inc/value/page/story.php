<?php
$p_key = "story";
${'page_' . $p_key} = defaultPageValue($p_key, array(
	'title' => setValueTitle('ストーリー', 'Story'),
	'date' => date($date_format),
	'class' => 'page-' . $p_key,
	'id' => 0,
	'slug' => $p_key,
	'post_type' => 'page',
	'section_mode' => $p_key,
	'nav' => setPageNav($p_key),
	'pankuzu' => '',
	'parent' => 'home',
	'taxonomy' => array(),
	'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
	'text' => '',
	'meta' => array(
		'ogp' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		'description' => '',
	),
	'thumbnail' => array(
		'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	),
));

registerAcfFromValue($p_key, $p_key . ' page', 'page', ${'page_' . $p_key});
