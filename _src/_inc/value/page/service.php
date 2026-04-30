<?php
$p_key = "service";
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
		'title' => setValueTitle('サービス1', "Service title"), //mainが日, subが英
		'date' => date($date_format),
		'class' => '',
		'id' => 0,
		'post_type' => $p_key,
		'slug' => 'detail.php?id=0',
		'section_mode' => $p_key . '_single',
		'pankuzu' => '',
		'parent' => $p_key, // ←親のmenu_listのkey
		'taxonomy' => array(),
		'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		'meta' => array(
			'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
			'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		),
		'thumbnail' => array(
			'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
			'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		),
		'mv' => array(
			'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
			'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		),
		'feature' => array(
			'title' => setValueTitle("サービスの特徴", "Feature"),
			'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text . $dummy_text,
			'list' => array(
				array(
					'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
					'title' => setValueTitle('見出しが入ります。'),
					'lead' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
					'link' => array(
						'mode' => 'link',
						'link' => array(
							'title' => 'VIEW MORE2',
							'url' => $link_path,
							'target' => '',

						),
					),
				),
				array(
					'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
					'title' => setValueTitle('見出しが入ります。'),
					'lead' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
					'link' => array(
						'mode' => 'link',
						'link' => array(
							'title' => 'VIEW MORE2',
							'url' => $link_path,
							'target' => '',

						),
					),
				),
				array(
					'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
					'title' => setValueTitle('見出しが入ります。'),
					'lead' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
					'link' => array(
						'mode' => 'link',
						'link' => array(
							'title' => 'VIEW MORE2',
							'url' => $link_path,
							'target' => '',
						),
					),
				),
			),
		),
		'point' => array(
			'title' => setValueTitle("ポイント", "Point"),
			'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text . $dummy_text,
			'list' => array(
				array(
					'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
					'title' => setValueTitle('見出しが入ります。'),
					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
					'link' => array(
						'mode' => 'link',
						'link' => array(
							'title' => 'VIEW MORE2',
							'url' => $link_path,
							'target' => '',

						),
					),
				),
				array(
					'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
					'title' => setValueTitle('見出しが入ります。'),
					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
					'link' => array(
						'mode' => 'link',
						'link' => array(
							'title' => 'VIEW MORE2',
							'url' => $link_path,
							'target' => '',

						),
					),
				),
				array(
					'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
					'title' => setValueTitle('見出しが入ります。'),
					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
					'link' => array(
						'mode' => 'link',
						'link' => array(
							'title' => 'VIEW MORE2',
							'url' => $link_path,
							'target' => '',
						),
					),
				),
			),
		),
		'table' => array(
			'title' => setValueTitle("表組での説明", "Table"),
			'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text . $dummy_text,
			'dl' => array(
				array(
					'dt' =>	$dummy_text,
					'dd' => $dummy_text . $dummy_text . $dummy_text,
				),
				array(
					'dt' =>	$dummy_text,
					'dd' => $dummy_text . $dummy_text . $dummy_text,
				),
				array(
					'dt' =>	$dummy_text,
					'dd' => $dummy_text . $dummy_text . $dummy_text,
				),
			),
		),
		'relate' => array(
			'title' => setValueTitle("関連記事へのリンク", "Relate"),
			'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text . $dummy_text,
			'relation' => array(
				'cpt' => 'topics',
				'max' => 3,
				'list' => array(0, 0, 0),
			),
			'link' => array(
				'mode' => "link",
				'link' => array(
					'title' => 'VIEW MORE',
					'url' => $link_path . '/',
					'target' => '',
				),
			),
		)
	),
	array(
		'title' => setValueTitle('サービス2', "Service title"), //mainが日, subが英
		'date' => date($date_format),
		'class' => '',
		'id' => 0,
		'post_type' => $p_key,
		'slug' => 'detail.php?id=0',
		'section_mode' => $p_key . '_single',
		'pankuzu' => '',
		'parent' => $p_key, // ←親のmenu_listのkey
		'taxonomy' => array(),
		'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		'meta' => array(
			'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
			'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		),
		'thumbnail' => array(
			'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
			'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		),
		'mv' => array(
			'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
			'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		),
	),
	array(
		'title' => setValueTitle('サービス3', "Service title"), //mainが日, subが英
		'date' => date($date_format),
		'class' => '',
		'id' => 0,
		'post_type' => $p_key,
		'slug' => 'detail.php?id=0',
		'pankuzu' => '',
		'parent' => $p_key, // ←親のmenu_listのkey
		'taxonomy' => array(),
		'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		'meta' => array(
			'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
			'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		),
		'thumbnail' => array(
			'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
			'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		),
		'mv' => array(
			'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
			'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		),
	),

);

${'page_' . $p_key}  = defaultPageValue(
	$p_key,
	array(
		'title' => setValueTitle('事業紹介', "Service"), //mainが日, subが英
		'section_mode' => $p_key . '_archive',
		'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		// 'meta' => array(
		// 	'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		// 	'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		// ),
		// 'thumbnail' => array(
		// 	'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
		// 	'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
		// ),

		'postList' => array(
			'cpt' => 'service',
			// 'num' => '20', wpで指定
			'pager' => false,
			'list' => array(0, 1, 2),
		),
	),
);


registerAcfFromValue($p_key, $p_key . '一覧', 'archive', ${'page_' . $p_key});

$acfvalues[] = addAcfValueArray($p_key, $p_key . '詳細', 'single', array(

	setAcfTitle('title', 'ページタイトル', array('main', 'sub'), 'table'),
	// setAcfMv(),
	array(
		'type' => "group",
		'label' => '特徴',
		'name' => 'feature',
		'layout' => 'block',
		'sub_fields' =>  array(
			setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
			setAcfText(),
			array(
				'type' => "repeater",
				'label' => 'リスト',
				'name' => 'list',
				'layout' => 'row',
				'button_label' => 'ポイントを追加',
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
		'label' => 'ポイント',
		'name' => 'point',
		'layout' => 'block',
		'sub_fields' =>  array(
			setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
			setAcfText(),
			array(
				'type' => "repeater",
				'label' => 'リスト',
				'name' => 'list',
				'layout' => 'row',
				'button_label' => 'ポイントを追加',
				'sub_fields' =>  array(
					setAcfImage(),
					setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
					setAcfText(),
					setAcfLink(),
				),
			),
		),
	),
	array(
		'type' => "group",
		'label' => '表ぐみでの説明',
		'name' => 'table',
		'layout' => 'block',
		'sub_fields' =>  array(
			setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
			setAcfText(),
			setAcfDl(),
		),
	),
	array(
		'type' => "group",
		'label' => '関連投稿',
		'name' => 'relate',
		'layout' => 'block',
		'sub_fields' =>  array(
			setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
			setAcfSelectPostList('relation', '関連投稿', 'table', 'service'),
			setAcfLink()
		),
	),

));
