<?php
$p_key = "_dev";
${'page_' . $p_key} = array(
	'title' => setValueTitle('スタイルガイド', "Style Guide"), //mainが日, subが英
	'date' => date($date_format),
	'class' => 'page-dev',
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
	'nav' => array(
		'list' => array(
			array(
				'url' => $link_path . '/_dev',
				'text' => 'JS-TOP',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=loadsequence',
				'text' => 'ページ読み込み時処理',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=contentmetrics',
				'text' => 'コンテンツ寸法設定',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=fixedsvh',
				'text' => 'SVHの処理の初回読み込み',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=scrollstate',
				'text' => '大枠のスクロール状態管理',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=headermetrics',
				'text' => 'ヘッダー高さの測定',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=footermetrics',
				'text' => 'フッター高さの測定',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=invertmode',
				'text' => 'ライト/ダークモード切り替え',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=sectionstate',
				'text' => 'セクション状態管理',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=invertparts',
				'text' => 'パーツ反転管理',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=scrollactionelements',
				'text' => 'オブジェクトのスクロール管理',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=anchorscroll',
				'text' => 'ページ内アンカー移動',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=lazybackground',
				'text' => '背景画像の遅延読み込み',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=hamburgermenu',
				'text' => 'ハンバーガーメニュー',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=tabs',
				'text' => 'タブ切り替え',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=accordion',
				'text' => 'アコーディオン',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=googlemap',
				'text' => 'GoogleMapAPI制御',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=slideshow',
				'text' => 'スライドショー',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=splidecontroller',
				'text' => 'Splide',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=youtubeplayers',
				'text' => 'YouTube',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=videoplayers',
				'text' => 'videoタグ',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=tileequalizer',
				'text' => 'Tile（高さ揃え）',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=formvalidation',
				'text' => 'フォーム入力チェック',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=modalmodule',
				'text' => '基本モーダル',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=modalgallerysplide',
				'text' => 'モーダル内 Splide ギャラリー',
			),

			array(
				'url' => $link_path . '/_dev/detail.php?mode=modalyoutube',
				'text' => 'モーダル内 YouTube',
			),
			array(
				'url' => $link_path . '/_dev/detail.php?mode=modalvideo',
				'text' => 'モーダル内 videoタグ',
			),
		),
	),
	'loadsequence' => array(
		'title' => setValueTitle('ページ読み込み時処理', "LoadSequence()"),
	),
	'contentmetrics' => array(
		'title' => setValueTitle('コンテンツ寸法設定', "ContentMetrics()"),
	),
	'fixedsvh' => array(
		'title' => setValueTitle('SVHの処理の初回読み込み', "FixedSVH()"),
	),
	'scrollstate' => array(
		'title' => setValueTitle('大枠のスクロール状態管理', "ScrollState()"),
	),
	'headermetrics' => array(
		'title' => setValueTitle('ヘッダー高さの測定', "HeaderMetrics()"),
	),
	'footermetrics' => array(
		'title' => setValueTitle('フッター高さの測定', "FooterMetrics()"),
	),
	'invertmode' => array(
		'title' => setValueTitle('ライト/ダークモード切り替え', "InvertMode()"),
	),
	'sectionstate' => array(
		'title' => setValueTitle('セクション状態管理）', "SectionState()"),
	),
	'invertparts' => array(
		'title' => setValueTitle('パーツ反転管理', "InvertParts()"),
	),
	'scrollactionelements' => array(
		'title' => setValueTitle('オブジェクトのスクロール管理', "ScrollActionElements()"),
	),
	'anchorscroll' => array(
		'title' => setValueTitle('ページ内アンカー移動', "AnchorScroll()"),
	),
	'lazybackground' => array(
		'title' => setValueTitle('背景画像の遅延読み込み', "LazyBackground()"),
	),
	'hamburgermenu' => array(
		'title' => setValueTitle('ハンバーガーメニュー', "HamburgerMenu()"),
	),
	'tabs' => array(
		'title' => setValueTitle('タブ切り替え', "Tabs()"),
	),
	'accordion' => array(
		'title' => setValueTitle('アコーディオン', "Accordion()"),
	),
	'googlemap' => array(
		'title' => setValueTitle('GoogleMapAPI制御', "GoogleMap()"),
	),
	'slideshow' => array(
		'title' => setValueTitle('スライドショー', "SlideShow()"),
	),
	'splidecontroller' => array(
		'title' => setValueTitle('Splide', "SplideController()"),
	),
	'youtubeplayers' => array(
		'title' => setValueTitle('YouTube', "YouTubePlayers()"),
	),
	'videoplayers' => array(
		'title' => setValueTitle('videoタグ', "VideoPlayers()"),
	),
	'tileequalizer' => array(
		'title' => setValueTitle('Tile（高さ揃え）', "TileEqualizer()"),
	),
	'formvalidation' => array(
		'title' => setValueTitle('フォーム入力チェック', "FormValidation()"),
	),
	'modalmodule' => array(
		'title' => setValueTitle('基本モーダル', "ModalModule()"),
	),
	'modalgallerysplide' => array(
		'title' => setValueTitle('モーダル内 Splide ギャラリー', "ModalGallerySplide()"),
	),
	'modalyoutube' => array(
		'title' => setValueTitle('モーダル内 YouTube', "ModalYouTube()"),
	),
	'modalvideo' => array(
		'title' => setValueTitle('モーダル内 videoタグ', "ModalVideo()"),
	),

	// 'styleguide' => array(
	// 	'logo' => array(
	// 		'class' => 'logo',
	// 		'title' => setValueTitle('ロゴ', "logo"),
	// 	),
	// 	'color' => array(
	// 		'class' => 'color',
	// 		'title' => setValueTitle('カラー', "color"),
	// 		'list' => array(
	// 			array(
	// 				'title' => setValueTitle('ベーシックカラー', "basic Color"),
	// 				'class' => 'is-pallet',
	// 				'list' => array(
	// 					'Title',
	// 					'Text',
	// 					'SubText',
	// 					'Border',
	// 				),
	// 			),
	// 			array(
	// 				'title' => setValueTitle('キーカラー', "key Color"),
	// 				'class' => 'is-key',
	// 				'list' => array(
	// 					'Key1',
	// 					'Key2',
	// 					'Key3',
	// 					'Attention',
	// 				),
	// 			),
	// 			array(
	// 				'title' => setValueTitle('カラー', "border Color"),
	// 				'class' => 'is-border',
	// 				'list' => array(
	// 					'Border',
	// 				),
	// 			),
	// 			array(
	// 				'title' => setValueTitle('キーカラー', "background Color"),
	// 				'class' => 'is-background',
	// 				'list' => array(
	// 					'Base1',
	// 					'Base2',
	// 					'Base3',
	// 					'White',
	// 				),
	// 			),

	// 		),
	// 	),
	// 	'font' => array(
	// 		'class' => 'font',
	// 		'title' => setValueTitle('フォント', "font"),
	// 		'list' => array(
	// 			array(
	// 				'var' => 'jp_go',
	// 				'main' => 'あア漢',
	// 				'sub' => 'この文章はダミーです。文字の大きさ、量、字間、行間等を確認するために入れています。この文章はダミーです。',
	// 			),
	// 			array(
	// 				'var' => 'en_go',
	// 				'main' => 'Aa1',
	// 				'sub' => 'This text is a dummy. It is included to check the size, amount, spacing, and line spacing of the text.',
	// 			),
	// 			array(
	// 				'var' => 'ff',
	// 				'main' => 'Aあaア1円',
	// 				'sub' => 'Google の無料サービスなら、単語、フレーズ、ウェブページを英語から 100 以上の他言語にすぐに翻訳できます。',
	// 			),
	// 		),
	// 	),
	// 	'title' => array(
	// 		'class' => 'title',
	// 		'title' => setValueTitle('タイトル', "title"),
	// 		'list' => array(
	// 			array(
	// 				'class' => 'p-title__sec',
	// 				'title' => setValueTitle('セクション見出し', "Section Title"),
	// 				'name' => 'Section Title',
	// 			),
	// 			array(
	// 				'class' => 'p-title__sub',
	// 				'title' => setValueTitle('サブタイトル', "Sub Title"),
	// 				'name' => 'Section Title',
	// 			),
	// 			array(
	// 				'class' => 'p-title__xxx',
	// 				'title' => setValueTitle('タイトル', "title"),
	// 				'name' => 'Section Title',
	// 			),
	// 			array(
	// 				'class' => 'p-title__xxx',
	// 				'title' => setValueTitle('タイトル', "title"),
	// 				'name' => 'Section Title',
	// 			),
	// 			array(
	// 				'class' => 'p-title__xxx',
	// 				'title' => setValueTitle('タイトル', "title"),
	// 				'name' => 'Section Title',
	// 			),
	// 		),
	// 	),
	// 	'text' => array(
	// 		'class' => 'text',
	// 		'title' => setValueTitle('テキスト', "text"),
	// 		'list' => array(
	// 			array(
	// 				'class' => 'p-lead',
	// 				'text' => 'この文章はダミーです。<br>文字の確認のために入れております。',
	// 				'name' => 'lead',
	// 			),
	// 			array(
	// 				'class' => 'p-text',
	// 				'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。<a href="#">テキストリンクはこちら</a>文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	// 				'name' => 'text',
	// 			),
	// 			array(
	// 				'class' => 'p-caption',
	// 				'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。<a href="#">テキストリンクはこちら</a>文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	// 				'name' => 'caption',
	// 			),
	// 		),
	// 	),
	// 	'button' => array(
	// 		'class' => 'button',
	// 		'title' => setValueTitle('テキスト', "button"),
	// 		'list' => array(
	// 			array(
	// 				'class' => 'p-button',
	// 				'text' => 'この文章はダミーです。<br>文字の確認のために入れております。',
	// 				'link' => array(
	// 					'mode' => "link",
	// 					'link' => array(
	// 						'title' => 'VIEW MORE',
	// 						'url' => $link_path . '/',
	// 						'target' => '',
	// 					),
	// 				),
	// 				'name' => '通常リンク',
	// 			),
	// 			array(
	// 				'class' => 'p-button',
	// 				'text' => 'この文章はダミーです。<br>文字の確認のために入れております。',
	// 				'link' => array(
	// 					'mode' => "link",
	// 					'link' => array(
	// 						'title' => 'VIEW MORE',
	// 						'url' => $link_path . '/',
	// 						'target' => '_blank',
	// 					),
	// 				),
	// 				'name' => '外部リンク',
	// 			),
	// 			array(
	// 				'class' => 'p-button',
	// 				'text' => 'この文章はダミーです。<br>文字の確認のために入れております。',
	// 				'link' => array(
	// 					'mode' => "link",
	// 					'link' => array(
	// 						'title' => 'VIEW MORE',
	// 						'url' => $link_path . '/xxx.pdf',
	// 						'target' => '_blank',
	// 					),
	// 				),
	// 				'name' => 'ファイルリンク',
	// 			),

	// 			array(
	// 				'class' => 'p-button is-color__sub',
	// 				'text' => 'この文章はダミーです。<br>文字の確認のために入れております。',
	// 				'link' => array(
	// 					'mode' => "link",
	// 					'link' => array(
	// 						'title' => 'VIEW MORE',
	// 						'url' => $link_path . '/',
	// 						'target' => '',
	// 					),
	// 				),
	// 				'name' => 'サブカラー',
	// 			),
	// 			array(
	// 				'class' => 'p-button is-color__reverse',
	// 				'text' => 'この文章はダミーです。<br>文字の確認のために入れております。',
	// 				'link' => array(
	// 					'mode' => "link",
	// 					'link' => array(
	// 						'title' => 'VIEW MORE',
	// 						'url' => $link_path . '/',
	// 						'target' => '',
	// 					),
	// 				),
	// 				'name' => '抜きボタン',
	// 			),
	// 			array(
	// 				'class' => 'p-button is-color__disable',
	// 				'text' => 'この文章はダミーです。<br>文字の確認のために入れております。',
	// 				'link' => array(
	// 					'mode' => "link",
	// 					'link' => array(
	// 						'title' => 'VIEW MORE',
	// 						'url' => $link_path . '/',
	// 						'target' => '',
	// 					),
	// 				),
	// 				'name' => '非活性ボタン',
	// 			),

	// 		),
	// 	),
	// 	'image' => array(
	// 		'class' => 'image',
	// 		'title' => setValueTitle('画像', "image"),
	// 		'list' => array(
	// 			array(
	// 				'class' => 'p-image',
	// 				'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
	// 				'name' => '通常画像',
	// 			),
	// 		),
	// 	),
	// 	'icon' => array(
	// 		'class' => 'icon',
	// 		'title' => setValueTitle('アイコン', "icon"),
	// 		'list' => array(
	// 			array(
	// 				'svg' => 'icon-arrow1',
	// 			),
	// 			array(
	// 				'svg' => 'icon-arrow2',
	// 			),
	// 			array(
	// 				'svg' => 'icon-arrow3',
	// 			),
	// 			array(
	// 				'svg' => 'icon-link',
	// 			),
	// 			array(
	// 				'svg' => 'icon-back',
	// 			),
	// 			array(
	// 				'svg' => 'icon-index',
	// 			),
	// 			array(
	// 				'svg' => 'icon-pdf',
	// 			),
	// 			array(
	// 				'svg' => 'sns-instagram',
	// 			),
	// 			array(
	// 				'svg' => 'sns-line',
	// 			),
	// 			array(
	// 				'svg' => 'sns-youtube',
	// 			),
	// 			array(
	// 				'svg' => 'sns-x',
	// 			),
	// 		),
	// 	),
	// 	'grid' => array(
	// 		'class' => 'grid',
	// 		'title' => setValueTitle('グリッド', "grid"),
	// 	),

	// ),
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
