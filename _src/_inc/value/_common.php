<?php
// function.phpで使用する変数のため、ここで定義
$site_title       = "サイトタイトル";
$site_description = "これはフォーマットです。";
$site_keywords    = "";
$client_name      = "株式会社xxxxxxxx";
$copyright      = "© 2023 Company Name Inc."; //footerに表示
$dummy_text =  'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。';
$dummy_text2 =  'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、<a href="#">リンクはこちら</a>です。';

$theme_color = '';
$telephone        = true; // true : 電話番号への自動リンクを消す
$viewport         = true; // true : viewportの指定あり
$ga = "";
$gtm = "";
$googlefont = array(
	'Roboto:wght@400;500;700',
	'Noto+Sans+JP:wght@400;500;700'
);

$zip =  "〒100-0001";
$add =  "東京都千代田区千代田1-1-1";
$buil =  "皇居ビル";
$mapurl = "https://maps.app.goo.gl/oaYVS9k4Qch3prYLA";
$logo = $local_path . "/assets/image/common/logo.svg";
$tel = "012-3456-7890";
$email = "a@delaunay.jp";
$noimage    = $image_path . '';
$common_ogp = $image_path . "common/ogp.webp";
$twittercard    = "summary_large_image"; // summary, summary_large_image, app , player
$map_api = "AIzaSyBqbFe8HiHPFKei49bolFbIujFtZHT6-eM";
$recaptchaSecret = 'xxxxx';
$recaptchaSite = 'xxxxx';

$date_format = 'Y.m.d';
$twitteraccount = ""; //metaにも設定がある
$snslist = array(
	array(
		'link' => 'https://www.facebook.com/',
		'icon' => 'sns-facebook',
		'text' => 'facebook',
	),
	array(
		'link' => 'https://twitter.com/' . $twitteraccount,
		'icon' => 'sns-x',
		'text' => 'x',
	),
	array(
		'link' => 'https://www.instagram.com/',
		'icon' => 'sns-instagram',
		'text' => 'instagram',
	),
	array(
		'link' => 'https://www.instagram.com/',
		'icon' => 'sns-note',
		'text' => 'note',
	),
);

$setting_title_preference = [
	'nav'     => 'main',
	'meta'    => 'main',
	'pankuzu' => 'main', // 必要に応じて'sub'など
];
