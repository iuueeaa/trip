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
			<style>
				/* .l-header,.l-header__hbg__icon{
		display: none;
	} */

				.js-fixed {
					/* background-color: green; */
					padding: 0 !important;
					position: relative;
				}

				.js-fixed__bg {
					position: absolute;
					width: 100%;
					height: 100vh;
					top: 0;
					left: 0;
					background-image: url(<?php echo $local_path; ?>/assets/image/_dummy/pic-dummy_image3.jpg);
					background-repeat: no-repeat;
					background-position: center;
					background-size: cover;
					transform-origin: center;
					will-change: opacity, transform;
					transform-origin: center center;
					transform: translate(0px, 0px) scale(1, 1);
					opacity: 0;
					transition: opacity 800ms cubic-bezier(0.25, 0.1, 0.25, 1) 0ms, transform 800ms cubic-bezier(0.25, 0.1, 0.25, 1) 0ms;

				}

				.js-fixed.is-fixed .js-fixed__bg {
					opacity: 1;
					transform: translate(0px, 0px) scale(1, 1);
				}

				.js-fixed__bg::before {
					content: "";
					position: absolute;
					width: 100%;
					height: 100%;
					background-color: #000000;
					top: 0;
					left: 0;
					opacity: 0.3;
					z-index: 0;
					display: block;
				}

				.js-fixed__wrap {
					position: relative;
					z-index: 1;
					padding-top: 10rem;
					padding-bottom: 10rem;
				}

				.js-fixed__wrap .p-text {
					color: #000;
					font-size: 5vw;
					transition: color 800ms cubic-bezier(0.25, 0.1, 0.25, 1) 0ms;
				}

				.js-fixed.is-fixed .js-fixed__wrap .p-text {
					/* opacity: 1; */
					color: #ffffff;
				}
			</style>
			<?php $TemplateClass = 'b-template__js'; ?>
			<div class="<?php echo $TemplateClass; ?>">
				<?php include("_nav.php"); ?>
				<section>
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ Fixed Section ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">
							<a href="#Anchor_area" class="p-button"><span>To Area</span></a>
							<!-- <div class="box js-sa__op" style="height: 50px;"><span>box</span></div> -->
						</div>
						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
<div class="js-sa__op"></div>
]]></math></code></pre>
						</div>
					</div>
				</section>
				<section style="background-color: var(--Base2);">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="area" style="width: 100%; height: 60vh;"><span>通常セクション</span></div>
					</div>
				</section>
				<section style="background-color: var(--Base3);">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="area" style="width: 100%; height: 60vh;"><span>通常セクション</span></div>
					</div>
				</section>
				<section class="js-fixed">
					<div class="js-fixed__bg"></div>
					<div class="js-fixed__wrap">
						<div style="width: 80%; margin-left: auto; margin-right: auto;">
							<p class="p-text"><span>吾輩わがはいは猫である。名前はまだ無い。<br>どこで生れたかとんと見当けんとうがつかぬ。<br>何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。<br>吾輩はここで始めて人間というものを見た。<br>しかもあとで聞くとそれは書生という人間中で一番獰悪どうあくな種族であったそうだ。<br>この書生というのは時々我々を捕つかまえて煮にて食うという話である。<br>しかしその当時は何という考もなかったから別段恐しいとも思わなかった。<br>ただ彼の掌てのひらに載せられてスーと持ち上げられた時何だかフワフワした感じがあったばかりである。<br>掌の上で少し落ちついて書生の顔を見たのがいわゆる人間というものの見始みはじめであろう。<br>この時妙なものだと思った感じが今でも残っている。<br>第一毛をもって装飾されべきはずの顔がつるつるしてまるで薬缶やかんだ。<br>その後ご猫にもだいぶ逢あったがこんな片輪かたわには一度も出会でくわした事がない。<br>のみならず顔の真中があまりに突起している。<br>そうしてその穴の中から時々ぷうぷうと煙けむりを吹く。<br>どうも咽むせぽくて実に弱った。<br>これが人間の飲む煙草たばこというものである事はようやくこの頃知った。★★★★★★★★★★★</span></p>
						</div>
					</div>
				</section>
				<section style="background-color: var(--Base2);">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="area" style="width: 100%; height: 60vh;"><span>通常セクション</span></div>
					</div>
				</section>

				<section style="background-color: var(--Base1);">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div id="Anchor_area" class="area" style="width: 100%; height: 60vh;"><span>通常セクション</span></div>
					</div>
				</section>





			</div>

			<br><br><br><br><br><br><br><br><br><br>




		</main>

		<?php include($inc_path . "_l-foot.php"); ?>
	</div>
	<?php include($inc_path . "_l-script.php"); ?>
</body>

</html>
