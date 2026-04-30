<?php
$p_key = "recruit";
${$p_key . '_list'}  = array(
	array(
		'title' => setValueTitle('マーケティング・セールス', "Marketing Sales"), //mainが日, subが英
		'date' => date($date_format),
		'class' => 'page-' . $p_key,
		'id' => 0,
		'post_type' => $p_key,
		'slug' => 'detail.php?id=0',
		'section_mode' => $p_key . '_single',
		'pankuzu' => '',
		'parent' => $p_key, // ←親のmenu_listのkey
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
		'recruit' => array(
			'fix' => array(
				'name' => 'マーケティング・セールス',
				'type' => '業務委託／契約社員／正社員',
				'content' => '- R&Dのお客様の支援<br />- 当社各種サービスのマーケティングセールス全般',
				'salary' => '入社時の想定年収500万円～1500万円 （スキル・経験に応じて優遇、年俸制）<br />給与改定年2回（4月、10月)',
				'area' => 'フルリモート<br />（お客様訪問時に出張あり）',
			),
			'dl' => array(
				array(
					'dt' => '福利厚生',
					'dd' => '交通費全額支給、社会保険完備（雇用・労災・健康・厚生年金）',
				),
				array(
					'dt' => '求める人材',
					'dd' => '- 自らマーケティングの施策を考案し実行できる方<br />- お客様へセールスができる方',
				),
				array(
					'dt' => 'その他',
					'dd' => '成果報酬型の業務委託も可能です。',
				),
			),
		),
	),
);

${'page_' . $p_key} = defaultPageValue(
	$p_key,
	array(
		'title' => setValueTitle('採用情報', "Recruit"), //mainが日, subが英
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
			'cpt' => 'recruit',
			// 'num' => '20',
			'pager' => false,
			'list' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
		)
	)
);


registerAcfFromValue($p_key, $p_key . '一覧', 'archive', ${'page_' . $p_key});
registerAcfFromValue($p_key, $p_key . '詳細', 'single', ${$p_key . '_list'}[0], array('title'));
