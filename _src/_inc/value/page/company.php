<?php

$p_key = "company";
${'page_' . $p_key} = defaultPageValue(
	$p_key,
	array(
		'title' => setValueTitle('企業情報', "Company"), //mainが日, subが英
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
		),
		'vision' => array(
			'title' => setValueTitle('ビジョン', "Vision"),
			'text' => $dummy_text . $dummy_text,
			'video' => array(
				'video' => $local_path . '/assets/files/dummy.mp4',
				'image' => setValueImage($image_path . '_dummy/pic-dummy.webp'),
			),
			'list' => array(
				array(
					'image' =>	setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
					'title' => setValueTitle("見出しが入ります", "title"),
					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
					'link' => array(
						'mode' => "link",
						'link' => array(
							'title' => 'VIEW MORE',
							'url' => $link_path . '/',
							'target' => '',
						),

					),
				),
				array(
					'image' =>	setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
					'title' => setValueTitle("見出しが入ります", "title"),
					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
					'link' => array(
						'mode' => "link",
						'link' => array(
							'title' => 'VIEW MORE',
							'url' => $link_path . '/',
							'target' => '',
						),

					),
				),
				array(
					'image' =>	setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
					'title' => setValueTitle("見出しが入ります", "title"),
					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
					'link' => array(
						'mode' => "link",
						'link' => array(
							'title' => 'VIEW MORE',
							'url' => $link_path . '/',
							'target' => '',
						),

					),
				),

			),
		),
		'outline' => array(
			'title' => setValueTitle('会社概要', "Outline"),
			'dl' => array(
				array(
					'th' => '見出しが入ります',
					'td' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
				),
				array(
					'th' => '見出しが入ります',
					'td' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
				),
				array(
					'th' => '見出しが入ります',
					'td' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
				),
			),
		),
		'history' => array(
			'title' => setValueTitle('沿革', "History"),
			'history2' => array(
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
