<?php
$p_key = "works";
${$p_key . '_value'} = array();
foreach ($custompostarray as $cpt) {
	if ($cpt['slug'] == $p_key) {
		foreach ($cpt['taxonomy'] as $thistaxonomy) {
			${$p_key . '_value'}[$thistaxonomy['slug']] = setValueTaxonmy($p_key, '_' . $thistaxonomy['slug'], $thistaxonomy['value']);
		}
	}
}
${$p_key . '_list'} = array(
	array(
		'title'        => setValueTitle('制作事例の見出しが入ります。'),
		'date'         => date($date_format),
		'class'        => 'page-' . $p_key,
		'id'           => 0,
		'post_type'    => $p_key,
		'slug'         => 'detail.php?id=0',
		'section_mode' => $p_key . '_single',
		'pankuzu'      => '',
		'parent'       => $p_key,
		'taxonomy'     => array(
			'category' => array(${$p_key . '_value'}['category'][0]),
		),
		'image'     => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		'thumbnail' => array(
			'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
			'text'  => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		),
		'mv' => array(
			'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
			'text'  => '',
		),
		'body' => array(),
	),
);
${'page_' . $p_key} = defaultPageValue($p_key, array(
	'title'        => setValueTitle('制作事例', "Works"),
	'section_mode' => $p_key . '_archive',
	'image'        => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
	'text'         => '',
	'thumbnail'    => array(
		'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		'text'  => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	),
	'mv'           => array(
		'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		'text'  => '',
	),
	'postList'     => array(
		'cpt'   => $p_key,
		'pager' => true,
		'list'  => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	),
));

foreach (${$p_key . '_value'} as $taxkey => $thistax) {
	${$p_key . '_' . $taxkey . '_list'} = array();
	foreach ($thistax as $tax) {
		$thistaxarr = ${'page_' . $p_key};
		$thistaxarr['title'] = setValueTitle($tax['name'], $p_key . ' ' . $taxkey);
		${$p_key . '_' . $taxkey . '_list'}[] = $thistaxarr;
	}
}

registerAcfFromValue($p_key, $p_key . ' 一覧', 'archive', ${'page_' . $p_key});

$acfvalues[] = addAcfValueArray($p_key, $p_key . ' 詳細', 'single', array(
	setAcfTitle('title', "ページタイトル", array('main', 'sub'), 'table'),
	setAcfMv(),
	setAcfBody2('body', $p_key, 'block'),
));
