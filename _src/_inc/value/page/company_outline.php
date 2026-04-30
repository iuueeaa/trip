<?php
$p_key = "company_outline";
${'page_' . $p_key} = defaultPageValue(
	$p_key,
	array(
		'title' => setValueTitle('会社概要', "Outline"), //mainが日, subが英
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
		'access' => array(
			'title' => setValueTitle('アクセス', "Access"),
			'map' => array(
				'mode' => 'api', //iframe,iframe_link,api
				'lat' => "35.32697306801492",
				'lng' => "139.43685625823144",
				'pin' => $image_path . "common/pin.webp",
				'iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3255.1619227589626!2d139.43445795155552!3d35.32680035683735!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x60184dfa76e1ae43%3A0xa5c57aa86b013e27!2z44CSMjUzLTAwMzEg56We5aWI5bed55yM6IyF44O25bSO5biC5a-M5aOr6KaL55S677yR77yW4oiS77yT77yZ!5e0!3m2!1sja!2sjp!4v1660287810047!5m2!1sja!2sjp" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>'
			)
		)
	),

);


registerAcfFromValue($p_key, $p_key . ' page', 'page', ${'page_' . $p_key});
