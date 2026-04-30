<?php
// [1] パス解決
$url = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__FILE__, 2)); // _incより上の階層へ
$url = ltrim($url, '/');
if (strpos($url, '/') === false) {
	// top階層
} else {
	$num = strpos($url, '/');
	$url = substr($url, 0, $num);
}
$local_path = is_numeric($url) ? '/' . $url : '';
$root_path = $_SERVER['DOCUMENT_ROOT'] . $local_path;
$link_path = $local_path;
$image_path = $local_path . '/assets/image/';
$inc_path = $root_path . '/assets/inc/';
$icon_path  = $image_path . 'icon/sprite.svg#';


// [2] グローバル変数セット
$lang = (isset($_GET['lang'])) ? $_GET['lang'] : 'ja';
$wpflg = false;
$protocol = (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
$site_url = $protocol . $_SERVER["HTTP_HOST"];


if (!$wpflg) {
	// デバッグ用の変数の省略をなくす(本番では使用しない)
	ini_set('xdebug.var_display_max_children', -1);
	ini_set('xdebug.var_display_max_data', -1);
	ini_set('xdebug.var_display_max_depth', -1);
}

// [3] サイト全体共通値
require_once($inc_path . "value/_common.php");
require_once($inc_path . "function/index.php");
require_once($inc_path . "value/{$lang}.php");

// [4] ページvalueの自動展開
global $this_page_value;
$this_page_value = loadPageValue(); // 通常ページ（静的・WP共通）


// [5] 必要な関数群
$spriteXml = $image_path . "icon/sprite.svg";
$iconselect = array();
if (file_exists($spriteXml)) {
	$spriteXmlData = simplexml_load_file($spriteXml);
	foreach ($spriteXmlData as $icon) {
		$iconselect[] = $icon['id'];
	}
}

if (!empty($confirmFlg) && $confirmFlg == 1) {
	include($inc_path . "meta/mail.php");
}

// [6] ここでmeta出力に使う変数を完全に決定する
$page_class = $this_page_value["class"] ?? "";
$bodyclass = $page_class ? $page_class . ' is-ready' : 'is-ready';
$page_title = strip_tags($this_page_value["meta"]["title"] ?? $this_page_value["title"]["main"] ?? "");
$page_description = strip_tags($this_page_value["meta"]["description"] ?? "");
$page_ogimage = $this_page_value["meta"]["ogp"]["sizes"]["large"] ?? $this_page_value["image"]["sizes"]["large"] ?? "";
$meta = [
	'title' => $page_title ? "$page_title | $site_title" : $site_title,
	'description' => $page_description ?: $site_description,
	'url' => $site_url . $_SERVER["REQUEST_URI"],
	'type' => "website",
	'ogimage' => $page_ogimage ?: $common_ogp,
	'site_title' => $site_title,
	'twittercard' => $twittercard,
	'twitteraccount' => $twitteraccount ?? "",
	'telephone' => $telephone ?? false,
	'viewport' => $viewport ?? false,
	'theme_color' => $theme_color ?? "",
	'favicon' => $image_path . "common/favicon.ico",
	'apple_icon' => $image_path . "common/apple-touch-icon.webp",
	'googlefont' => $googlefont ?? [],
	'lang' => $lang,
];

// $localhostURL = $this_page['host'] ?? 'localhost';
// $localhostURL = gethostname();
$localhostURL = gethostbyname(gethostname());
// $localhostURL ='192.168.1.22';

function cssVars($cssVarName) {
	$cssFilePath = '../assets/css/style.css';
	$cssContent = file_get_contents($cssFilePath);
	if (preg_match('/' . preg_quote($cssVarName, '/') . ':\s*([^;}]+)/', $cssContent, $matches)) {
		// if (preg_match('/' . preg_quote($cssVarName) . ':\s*([^;]+)/', $cssContent, $matches)) {
		// if (preg_match('/' . preg_quote($cssVarName) . ':\s*([^;}\s]+)/', $cssContent, $matches)) {
		$cssVarValue = trim($matches[1]);
		if (preg_match('/var\((--[^\)]+)\)/', $cssVarValue, $varMatches)) {
			// return $varMatches[1];
			return cssVars($varMatches[1]);
		} else {
			return $cssVarValue;
		}
	} else {
		return "not found.";
	}
}
?>


<!DOCTYPE html>
<html dir="ltr" lang="<?php echo $lang ?>">

<head>
	<?php include($inc_path . "meta/meta.php"); ?>
	<?php include($inc_path . "meta/tag_head.php"); ?>
</head>

<body class="<?php echo $bodyclass ?>">
	<?php include($inc_path . "meta/tag_body.php"); ?>
	<?php if ($wpflg): ?>
		<div id="Guide"><span></span></div>
	<?php endif; ?>
	<div id="wrapAll">
		<?php include($inc_path . "_l-header.php"); ?>

		<main class="l-main">

			<?php $TemplateClass = 'b-template__js'; ?>
			<div class="<?php echo $TemplateClass; ?>">
				<?php include("_nav.php"); ?>
				<section id="Youtube">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ Youtube ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">
							<div class="js-youtube" data-id="jUNz-uTF--E" data-ratio="56.25"></div>
						</div>
						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
<div class="js-youtube" data-id="Youtube ID"></div>
data-id="ID"
data-ratio="56.25"
data-controls="true" //true false
data-mute="true" //true false
data-loop="false" //true false
data-auto="false" //true false
data-cover="src" //image path
]]></math></code></pre>
						</div>
					</div>
				</section>

				<section id="Youtube">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ Youtube ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">
							<div class="js-youtube" data-id="QaTR945SfHs" data-auto="true"></div>
						</div>
						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
<div class="js-youtube" data-id="Youtube ID" data-auto="true"></div>
//autoのときは強制的にloop=1,mute=1,controls=0
]]></math></code></pre>
						</div>
					</div>
				</section>

				<section id="Youtube">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ Youtube ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">
							<div class="js-youtube" data-id="jUNz-uTF--E" data-cover="http://img.youtube.com/vi/jUNz-uTF--E/sddefault.webp"></div>
						</div>
						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
<div class="js-youtube" data-id="Youtube ID" data-cover="http://img.youtube.com/vi/Youtube ID/sddefault.webp"></div>
//Youtube動画のはじまり(120X90)：1.jpg
//Youtube動画の中間タイム(120X90)：2.jpg
//Youtube動画の終了タイム(120X90)：3.jpg
//高クオリティのサムネイル(480x360) : hqdefault.jpg
//中クオリティのサムネイル(320x180) : mqdefault.jpg
//標準クオリティのサムネイル(120x90) : default.jpg
//HQ動画の標準クオリティのサムネイル(640x480) : default.jpg
//FULLHDのクオリティのサムネイル(1920x1080) : maxresdefault.jpg
]]></math></code></pre>
						</div>
					</div>
				</section>
			</div>

		</main>

		<?php include($inc_path . "_l-foot.php"); ?>
	</div>
	<?php include($inc_path . "_l-script.php"); ?>
</body>

</html>
