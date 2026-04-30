<?php
$p_key = "styleguide";
${'page_' . $p_key} = array(
	'title' => setValueTitle('スタイルガイド', "Style Guide"), //mainが日, subが英
	'date' => date($date_format),
	'class' => 'page-' . $p_key,
	'id' => 0,
	'slug' => $p_key,
	'section_mode' => $p_key,
	'pankuzu' => array('home'),
	// 'nav' => $menu_list[$p_key],
	'taxnomy' => array(),
	'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
	'meta' => array(
		'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	),
	'thumbnail' => array(
		'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	),
	'styleguide' => array(
		'logo' => array(
			'class' => 'logo',
			'title' => setValueTitle('ロゴ', "logo"),
		),
		'color' => array(
			'class' => 'color',
			'title' => setValueTitle('カラー', "color"),
			'list' => array(
				array(
					'title' => setValueTitle('ベーシックカラー', "basic Color"),
					'class' => 'is-pallet',
					'list' => array(
						'Title',
						'Text',
						'SubText',
						'Border',
					),
				),
				array(
					'title' => setValueTitle('キーカラー', "key Color"),
					'class' => 'is-key',
					'list' => array(
						'Key1',
						'Key2',
						'Key3',
						'Attention',
					),
				),
				array(
					'title' => setValueTitle('カラー', "border Color"),
					'class' => 'is-border',
					'list' => array(
						'Border',
					),
				),
				array(
					'title' => setValueTitle('キーカラー', "background Color"),
					'class' => 'is-background',
					'list' => array(
						'Base1',
						'Base2',
						'Base3',
						'White',
					),
				),

			),
		),
		'font' => array(
			'class' => 'font',
			'title' => setValueTitle('フォント', "font"),
			'list' => array(
				array(
					'var' => 'jp_go',
					'main' => 'あア漢',
					'sub' => 'この文章はダミーです。文字の大きさ、量、字間、行間等を確認するために入れています。この文章はダミーです。',
				),
				array(
					'var' => 'en_go',
					'main' => 'Aa1',
					'sub' => 'This text is a dummy. It is included to check the size, amount, spacing, and line spacing of the text.',
				),
				array(
					'var' => 'ff',
					'main' => 'Aあaア1円',
					'sub' => 'Google の無料サービスなら、単語、フレーズ、ウェブページを英語から 100 以上の他言語にすぐに翻訳できます。',
				),
			),
		),
		'title' => array(
			'class' => 'title',
			'title' => setValueTitle('タイトル', "title"),
			'list' => array(
				array(
					'class' => 'p-title__sec',
					'title' => setValueTitle('セクション見出し', "Section Title"),
					'name' => 'Section Title',
				),
				array(
					'class' => 'p-title__sub',
					'title' => setValueTitle('サブタイトル', "Sub Title"),
					'name' => 'Section Title',
				),
				array(
					'class' => 'p-title__xxx',
					'title' => setValueTitle('タイトル', "title"),
					'name' => 'Section Title',
				),
				array(
					'class' => 'p-title__xxx',
					'title' => setValueTitle('タイトル', "title"),
					'name' => 'Section Title',
				),
				array(
					'class' => 'p-title__xxx',
					'title' => setValueTitle('タイトル', "title"),
					'name' => 'Section Title',
				),
			),
		),
		'text' => array(
			'class' => 'text',
			'title' => setValueTitle('テキスト', "text"),
			'list' => array(
				array(
					'class' => 'p-lead',
					'text' => 'この文章はダミーです。<br>文字の確認のために入れております。',
					'name' => 'lead',
				),
				array(
					'class' => 'p-text',
					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。<a href="#">テキストリンクはこちら</a>文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
					'name' => 'text',
				),
				array(
					'class' => 'p-caption',
					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。<a href="#">テキストリンクはこちら</a>文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
					'name' => 'caption',
				),
			),
		),
		'button' => array(
			'class' => 'button',
			'title' => setValueTitle('テキスト', "button"),
			'list' => array(
				array(
					'class' => 'p-button',
					'text' => 'この文章はダミーです。<br>文字の確認のために入れております。',
					'link' => array(
						'mode' => "link",
						'link' => array(
							'title' => 'VIEW MORE',
							'url' => $link_path . '/',
							'target' => '',
						),
					),
					'name' => '通常リンク',
				),
				array(
					'class' => 'p-button',
					'text' => 'この文章はダミーです。<br>文字の確認のために入れております。',
					'link' => array(
						'mode' => "link",
						'link' => array(
							'title' => 'VIEW MORE',
							'url' => $link_path . '/',
							'target' => '_blank',
						),
					),
					'name' => '外部リンク',
				),
				array(
					'class' => 'p-button',
					'text' => 'この文章はダミーです。<br>文字の確認のために入れております。',
					'link' => array(
						'mode' => "link",
						'link' => array(
							'title' => 'VIEW MORE',
							'url' => $link_path . '/xxx.pdf',
							'target' => '_blank',
						),
					),
					'name' => 'ファイルリンク',
				),

				array(
					'class' => 'p-button is-color__sub',
					'text' => 'この文章はダミーです。<br>文字の確認のために入れております。',
					'link' => array(
						'mode' => "link",
						'link' => array(
							'title' => 'VIEW MORE',
							'url' => $link_path . '/',
							'target' => '',
						),
					),
					'name' => 'サブカラー',
				),
				array(
					'class' => 'p-button is-color__reverse',
					'text' => 'この文章はダミーです。<br>文字の確認のために入れております。',
					'link' => array(
						'mode' => "link",
						'link' => array(
							'title' => 'VIEW MORE',
							'url' => $link_path . '/',
							'target' => '',
						),
					),
					'name' => '抜きボタン',
				),
				array(
					'class' => 'p-button is-color__disable',
					'text' => 'この文章はダミーです。<br>文字の確認のために入れております。',
					'link' => array(
						'mode' => "link",
						'link' => array(
							'title' => 'VIEW MORE',
							'url' => $link_path . '/',
							'target' => '',
						),
					),
					'name' => '非活性ボタン',
				),

			),
		),
		'image' => array(
			'class' => 'image',
			'title' => setValueTitle('画像', "image"),
			'list' => array(
				array(
					'class' => 'p-image',
					'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
					'name' => '通常画像',
				),
			),
		),
		'icon' => array(
			'class' => 'icon',
			'title' => setValueTitle('アイコン', "icon"),
			'list' => array(
				array(
					'svg' => 'icon-arrow1',
				),
				array(
					'svg' => 'icon-arrow2',
				),
				array(
					'svg' => 'icon-arrow3',
				),
				array(
					'svg' => 'icon-link',
				),
				array(
					'svg' => 'icon-back',
				),
				array(
					'svg' => 'icon-index',
				),
				array(
					'svg' => 'icon-pdf',
				),
				array(
					'svg' => 'sns-instagram',
				),
				array(
					'svg' => 'sns-line',
				),
				array(
					'svg' => 'sns-youtube',
				),
				array(
					'svg' => 'sns-x',
				),
			),
		),
		'grid' => array(
			'class' => 'grid',
			'title' => setValueTitle('グリッド', "grid"),
		),

	),
	// 'mv' => array(
	// 	'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
	// 	'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	// ),
	// 'message' => array(
	// 	'title' => setValueTitle('メッセージ', "Message"),
	// 	'imageText' => array(
	// 		'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
	// 		'title' => '見出しが入ります',
	// 		'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	// 		'link' => array(
	// 			'mode' => "link",
	// 			'link' => array(
	// 				'title' => 'VIEW MORE',
	// 				'url' => $link_path . '/',
	// 				'target' => '',
	// 			),

	// 		),
	// 	),
	// 	'gallery' => array(
	// 		'gallery' =>	array(
	// 			setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
	// 			setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
	// 			setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
	// 			setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
	// 		),
	// 	),
	// 	'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text . $dummy_text . $dummy_text,
	// ),
	// 'vision' => array(
	// 	'title' => setValueTitle('ビジョン', "Vision"),
	// 	'text' => $dummy_text . $dummy_text,
	// 	'video' => array(
	// 		'video' => $rocal_path . '/assets/files/dummy.mp4',
	// 		'image' => setValueImage($image_path . '_dummy/pic-dummy.webp'),
	// 	),
	// 	'list' => array(
	// 		array(
	// 			'image' =>	setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
	// 			'title' => setValueTitle("見出しが入ります", "title"),
	// 			'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	// 			'link' => array(
	// 				'mode' => "link",
	// 				'link' => array(
	// 					'title' => 'VIEW MORE',
	// 					'url' => $link_path . '/',
	// 					'target' => '',
	// 				),

	// 			),
	// 		),
	// 		array(
	// 			'image' =>	setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
	// 			'title' => setValueTitle("見出しが入ります", "title"),
	// 			'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	// 			'link' => array(
	// 				'mode' => "link",
	// 				'link' => array(
	// 					'title' => 'VIEW MORE',
	// 					'url' => $link_path . '/',
	// 					'target' => '',
	// 				),

	// 			),
	// 		),
	// 		array(
	// 			'image' =>	setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
	// 			'title' => setValueTitle("見出しが入ります", "title"),
	// 			'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	// 			'link' => array(
	// 				'mode' => "link",
	// 				'link' => array(
	// 					'title' => 'VIEW MORE',
	// 					'url' => $link_path . '/',
	// 					'target' => '',
	// 				),

	// 			),
	// 		),

	// 	),
	// ),
	// 'outline' => array(
	// 	'title' => setValueTitle('会社概要', "Outline"),
	// 	'dl' => array(
	// 		array(
	// 			'th' => '見出しが入ります',
	// 			'td' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	// 		),
	// 		array(
	// 			'th' => '見出しが入ります',
	// 			'td' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	// 		),
	// 		array(
	// 			'th' => '見出しが入ります',
	// 			'td' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	// 		),
	// 	),
	// ),
	// 'history' => array(
	// 	'title' => setValueTitle('沿革', "History"),
	// 	'history' => array(
	// 		array(
	// 			'year' => '2023',
	// 			'list' => array(
	// 				array(
	// 					'month' => '7',
	// 					'title' => 'この文章はダミーです。',
	// 					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	// 				),
	// 				array(
	// 					'month' => '7',
	// 					'title' => 'この文章はダミーです。',
	// 					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	// 				),
	// 			),
	// 		),
	// 		array(
	// 			'year' => '2022',
	// 			'list' => array(
	// 				array(
	// 					'month' => '7',
	// 					'title' => 'この文章はダミーです。',
	// 					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	// 				),
	// 				array(
	// 					'month' => '7',
	// 					'title' => 'この文章はダミーです。',
	// 					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	// 				),
	// 			),
	// 		),
	// 	),
	// ),
);


// $acfvalues[] =	addAcfValueArray($p_key, $p_key . ' page', 'page', array(
// 	setAcfTitle('title', 'ページタイトル', array('main', 'sub'), 'table'),
// 	setAcfMv(),
// 	array(
// 		'type' => "group",
// 		'label' => 'メッセージ',
// 		'name' => 'message',
// 		'layout' => 'block',
// 		'sub_fields' =>  array(
// 			setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),

// 			array(
// 				'type' => "group",
// 				'label' => 'ボックス',
// 				'name' => 'imageText',
// 				'layout' => 'row',
// 				'sub_fields' =>  array(
// 					setAcfImage(),
// 					setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
// 					setAcfText(),
// 					setAcfLink(),
// 				),
// 			),
// 			setAcfGallery(),
// 			setAcfText(),
// 		),
// 	),
// 	array(
// 		'type' => "group",
// 		'label' => 'ビジョン',
// 		'name' => 'vision',
// 		'layout' => 'block',
// 		'sub_fields' =>  array(
// 			setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
// 			setAcfText(),
// 			setAcfVideo(),
// 			array(
// 				'type' => "repeater",
// 				'label' => 'リスト',
// 				'name' => 'list',
// 				'layout' => 'row',
// 				'button_label' => 'リストを追加',
// 				'sub_fields' =>  array(
// 					setAcfImage(),
// 					setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
// 					setAcfText(),
// 					setAcfLink(),
// 				),
// 			),
// 		),
// 	),
// 	array(
// 		'type' => "group",
// 		'label' => '会社概要',
// 		'name' => 'outline',
// 		'layout' => 'block',
// 		'sub_fields' =>  array(
// 			setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
// 			setAcfTable3('dl'),
// 		),
// 	),
// 	array(
// 		'type' => "group",
// 		'label' => '沿革',
// 		'name' => 'history',
// 		'layout' => 'block',
// 		'sub_fields' =>  array(
// 			setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
// 			setAcfHistory(),
// 		),
// 	),
// ));
