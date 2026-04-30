<?php
$p_key = "company_message";
${'page_' . $p_key} = defaultPageValue(
	$p_key,
	array(
		'title' => setValueTitle('メッセージ', "Message"), //mainが日, subが英
		'class' => 'page-company',
		'parent' => 'company', // ←親のmenu_listのkey
		'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		// 'meta' => array(
		// 'title'=>'',
		// 	'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		// 	'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		// ),
		// 'thumbnail' => array(
		// 	'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		// 	'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		// ),

		'message' => array(
			'title' => setValueTitle('メッセージ', "Message"),
			'imageText' => array(
				'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
				'title' => '見出しが入ります',
				'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
				'link' => array(
					'mode' => "link",
					'link' => array(
						'title' => 'VIEW MORE2',
						'url' => $link_path . '/',
						'target' => '',
					),

				),
			),
			'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text . $dummy_text . $dummy_text,
			'sign' => array(
				'date' => '制定日 : 2023.01.01／更新日 : 2023.06.04',
				'title' => '代表取締役',
				'people' => '山田 太郎',
			),
		),
	),
);


registerAcfFromValue($p_key, $p_key . ' page', 'page', ${'page_' . $p_key});
