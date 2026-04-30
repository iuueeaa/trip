<?php
$p_key = "company_vision";
${'page_' . $p_key} = defaultPageValue(
	$p_key,
	array(
		'title' => setValueTitle('ビジョン', "Vision"), //mainが日, subが英
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

		'vision' => array(
			'title' => setValueTitle('ビジョン', "Vision"),
			'text' => $dummy_text . $dummy_text,
			'youtube' => array(
				'id' => 'aqa9h-nL-TA',
				'image' => setValueImage($image_path . '_dummy/pic-dummy.webp'),
			),
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
	),
);


registerAcfFromValue($p_key, $p_key . ' page', 'page', ${'page_' . $p_key});
