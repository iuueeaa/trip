<?php
$p_key = "company_outline_history";
${'page_' . $p_key} = defaultPageValue(
	$p_key,
	array(
		'title' => setValueTitle('会社沿革', "History"), //mainが日, subが英
		'class' => 'page-company',
		'parent' => 'outline', // ←親のmenu_listのkey
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

		'history' => array(
			'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
			'title' => setValueTitle('沿革', "History"),
			'history' => array(
				array(
					'year' => '2023',
					'list' => array(
						array(
							'month' => '7',
							'title' => 'この文章はダミーです。',
							'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
						),
						array(
							'month' => '7',
							'title' => 'この文章はダミーです。',
							'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
						),
					),
				),
				array(
					'year' => '2022',
					'list' => array(
						array(
							'month' => '7',
							'title' => 'この文章はダミーです。',
							'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
						),
						array(
							'month' => '7',
							'title' => 'この文章はダミーです。',
							'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
						),
					),
				),
			),
		),
	),
);


registerAcfFromValue($p_key, $p_key . ' page', 'page', ${'page_' . $p_key});
