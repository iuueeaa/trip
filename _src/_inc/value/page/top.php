<?php
$p_key = "top";
${'page_' . $p_key} = defaultPageValue(
	$p_key,
	array(
		'nav' => $menu_list["home"],
		'parent' => null,
		'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		'test' => array(
			'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
			'title' => setValueTitle('私たちについて', 'About'),
			'lead' => $dummy_text,
			'text' => $dummy_text . $dummy_text . $dummy_text,
			'link' => setValueLink($link_path . "/", "VIEW MORE"),
			'list' => array(
				array(
					'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
					'title' => setValueTitle('私たちについて', 'About'),
					'lead' => $dummy_text,
					'text' => $dummy_text . $dummy_text . $dummy_text,
					'link' => setValueLink($link_path . "/", "VIEW MORE"),
				),
				array(
					'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
					'title' => setValueTitle('私たちについて', 'About'),
					'lead' => $dummy_text,
					'text' => $dummy_text . $dummy_text . $dummy_text,
					'link' => setValueLink($link_path . "/", "VIEW MORE"),
				),
				array(
					'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
					'title' => setValueTitle('私たちについて', 'About'),
					'lead' => $dummy_text,
					'text' => $dummy_text . $dummy_text . $dummy_text,
					'link' => setValueLink($link_path . "/", "VIEW MORE"),
				),
			),
			'postList' => array(
				'cpt' => 'news',
				'num' => '5', // wpで指定
				'pager' => false,
				'list' => array(0, 0, 0, 0, 0),
			),
		),
		// 'about' => array(
		// 	'title' => setValueTitle('私たちについて', 'About'),
		// 	'imageText' => array(
		// 		'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
		// 		'title' => '見出しが入ります',
		// 		'lead' => $dummy_text,
		// 		'text' => $dummy_text . $dummy_text . $dummy_text,
		// 		'link' => array(
		// 			'mode' => "link",
		// 			'link' => array(
		// 				'title' => 'VIEW MORE',
		// 				'url' => $link_path . '/',
		// 				'target' => '',
		// 			),
		// 		),
		// 	),
		// ),
		// 'company' => array(
		// 	'title' => setValueTitle('会社情報', 'Company'),
		// 	'imageText' => array(
		// 		'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
		// 		'title' => '見出しが入ります',
		// 		'lead' => $dummy_text,
		// 		'text' => $dummy_text . $dummy_text . $dummy_text,
		// 		'link' => array(
		// 			'mode' => "link",
		// 			'link' => array(
		// 				'title' => 'VIEW MORE',
		// 				'url' => $link_path . '/',
		// 				'target' => '',
		// 			),
		// 		),
		// 	),
		// 	'relation' => array(
		// 		'cpt' => 'page',
		// 		'list' => array(), // WP環境: 実際のページIDを指定
		// 	),
		// ),

		// 'service' => array(
		// 	'title' => setValueTitle('事業内容', 'service'),
		// 	'postList' => array(
		// 		'cpt' => 'service',
		// 		'list' => array(0, 1, 2),
		// 	),
		// 	'link' => array(
		// 		'mode' => "link",
		// 		'link' => array(
		// 			'title' => 'VIEW MORE',
		// 			'url' => $link_path . '/',
		// 			'target' => '',
		// 		),
		// 	),
		// ),
		// 'news' => array(
		// 	'title' => setValueTitle('お知らせ', 'news'),
		// 	'postList' => array(
		// 		'cpt' => 'news',
		// 		'num' => '5', // wpで指定
		// 		'pager' => false,
		// 		'list' => array(0, 0, 0, 0, 0),
		// 	),
		// 	'link' => array(
		// 		'mode' => "link",
		// 		'link' => array(
		// 			'title' => 'VIEW MORE',
		// 			'url' => $link_path . '/',
		// 			'target' => '',
		// 		),
		// 	),
		// ),
	),
);


$acfvalues["top"] =	addAcfValueArray('top', 'トップ', 'page', array(
	setAcfTitle('title', 'ページタイトル', array('main', 'sub'), 'table'),
	// setAcfMv(),
	array(
		'type' => "group",
		'label' => '私たちについて',
		'name' => 'about',
		'layout' => 'block',
		'sub_fields' =>  array(
			setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
			setAcfText(),
			array(
				'type' => "group",
				'label' => 'リスト',
				'name' => 'imageText',
				'layout' => 'row',
				'sub_fields' =>  array(
					setAcfImage(),
					setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
					setAcfText('lead', 'リード'),
					setAcfText(),
					setAcfLink(),
				),
			),
		),
	),
	array(
		'type' => "group",
		'label' => '会社概要',
		'name' => 'company',
		'layout' => 'block',
		'sub_fields' =>  array(
			setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
			setAcfText(),
			array(
				'type' => "group",
				'label' => 'リスト',
				'name' => 'imageText',
				'layout' => 'row',
				'sub_fields' =>  array(
					setAcfImage(),
					setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
					setAcfText('lead', 'リード'),
					setAcfText(),
					setAcfLink(),
				),
			),
			setAcfSelectPostList('selectPostList', '下層ページ', 'table', 'page', 4),
		),
	),
	array(
		'type' => "group",
		'label' => 'サービス',
		'name' => 'service',
		'layout' => 'block',
		'sub_fields' =>  array(
			setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
			setAcfSelectPostList('selectPostList', '関連投稿', 'table', 'service'),
			setAcfLink()
		),
	),
	array(
		'type' => "group",
		'label' => 'お知らせ',
		'name' => 'news',
		'layout' => 'block',
		'sub_fields' =>  array(
			setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
			setAcfPostList(),
			// array(
			// 	'type' => 'message',
			// 	'label' => 'お知らせ',
			// 	'name' => '',
			// 	'message' => '最新のお知らせが5件並びます。'
			// ),
			setAcfLink()
		),
	),
));
