<?php

$contactSection = array(
	'title' => array(
		'main' => 'お問い合わせ',
		'sub' => 'Contact',
	),
	'text' => 'お問い合わせ・ご相談は下記より承っております。',
	'links' => array(
		array(
			'link' => array(
				'title' => 'CONTACT US',
				'url' => '/contact',
				'target' => '',
			),
			'icon' => array(
				"path" => 'icon-arrow',
				"r" => false,
			),
			'color' => 'p-button',
		),
	),
	'list' => array(
		array(
			'icon' => 'icon-question2',
			'main' => 'FAQ',
			'sub' => 'よくあるご質問',
			'link' => array(
				'title' => '',
				'url' => '/contact/#faq',
				'target' => '',
			),
		),
		array(
			'icon' => 'icon-info2',
			'main' => 'FLOW',
			'sub' => 'ご利用の流れ',
			'link' => array(
				'title' => '',
				'url' => '/service#flow',
				'target' => '',
			),
		),
		array(
			'icon' => 'icon-person2',
			'main' => 'RECRUIT',
			'sub' => '採用情報',
			'link' => array(
				'title' => '',
				'url' => '/recruit',
				'target' => '',
			),
		),
		array(
			'icon' => 'icon-file2',
			'main' => 'COMPANY',
			'sub' => '企業情報',
			'link' => array(
				'title' => '',
				'url' => '/company',
				'target' => '',
			),
		),
	),
);

// sitemap.jsonより$menu_listを生成


/*-------------------------------------
*
* 各ページのvalue
*
-------------------------------------*/
include('page/news.php');
include('page/about.php');
include('page/topics.php');
include('page/company_vision.php');
include('page/company_history.php');
include('page/company_message.php');
include('page/company_outline.php');
include('page/company.php');
include('page/case.php');
include('page/recruit.php');
include('page/recruitform.php');
include('page/privacy.php');
include('page/terms.php');
include('page/contact.php');
include('page/service.php');
include('page/faq.php');
include('page/top.php');
include('page/styleguide.php');
include('page/company_outline_history.php');
include('page/works.php');
include('page/story.php');


$sectionTemplate = array(
	'title' => setValueTitle('日本語見出し', 'English Title'),
	'lead' => $dummy_text,
	'text' => $dummy_text . $dummy_text . $dummy_text,
	'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
	'link' => array(
		'mode' => "link",
		'link' => array(
			'title' => 'VIEW MORE',
			'url' => $link_path . '/',
			'target' => '',
		),
	),
	'list' => array(
		array(
			'title' => setValueTitle('日本語見出し', 'English Title'),
			'lead' => $dummy_text,
			'text' => $dummy_text . $dummy_text . $dummy_text,
			'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
			'link' => array(
				'mode' => "link",
				'link' => array(
					'title' => 'VIEW MORE',
					'url' => $link_path . '/xxxxx',
					'target' => '_self',
				),
			),
		),
		array(
			'title' => setValueTitle('日本語見出し', 'English Title'),
			'lead' => $dummy_text,
			'text' => $dummy_text . $dummy_text . $dummy_text,
			'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
			'link' => array(
				'mode' => "link",
				'link' => array(
					'title' => 'VIEW MORE',
					'url' => $link_path . '/xxxxx',
					'target' => '_self',
				),
			),
		),
		array(
			'title' => setValueTitle('日本語見出し', 'English Title'),
			'lead' => $dummy_text,
			'text' => $dummy_text . $dummy_text . $dummy_text,
			'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
			'link' => array(
				'mode' => "link",
				'link' => array(
					'title' => 'VIEW MORE',
					'url' => $link_path . '/xxxxx',
					'target' => '_self',
				),
			),
		),
	),
);
