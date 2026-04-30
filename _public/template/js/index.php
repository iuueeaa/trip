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
				<section id="Anchor">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ Anchor ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">
							<a href="#Anchor_area" class="p-button"><span>To Area</span></a>
							<br><br><br><br><br><br><br>
							<div id="Anchor_area" class="area" style="width: 100%; height: 100px;"><span>Area</span></div>
						</div>
						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
<a href="#Anchor_area">To Area</a>
<div id="TragetId"></div>
]]></math></code></pre>
						</div>
					</div>
				</section>
				<section id="ToTop">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ ToTop ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">
							<a href="javascript:void(0);" class="p-button js-totop"><span>TO TOP</span></a>
						</div>
						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
<a href="javascript:void(0);" class="js-totop"><span>TO TOP</span></a>

//フッター追従のときは
<a href="javascript:void(0);" class="js-totop__float"><span>TO TOP</span></a>
]]></math></code></pre>
						</div>
					</div>
				</section>
				<section id="Slide">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ Slide ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">
							<div class="js-slide" data-arrow="true" data-dots="true" data-interval="6000">
								<ul class="js-slide__ul">
									<li class="js-slide__li"><span class="js-slide__image js-lazy_bgi" data-bgi="<?php echo $local_path; ?>/assets/image/_dummy/pic-dummy.webp"></span></li>
									<li class="js-slide__li"><span class="js-slide__image js-lazy_bgi" data-bgi="<?php echo $local_path; ?>/assets/image/_dummy/pic-dummy_b.webp"></span></li>
									<li class="js-slide__li"><span class="js-slide__image js-lazy_bgi" data-bgi="<?php echo $local_path; ?>/assets/image/_dummy/pic-dummy.webp"></span></li>
									<li class="js-slide__li"><span class="js-slide__image js-lazy_bgi" data-bgi="<?php echo $local_path; ?>/assets/image/_dummy/pic-dummy_b.webp"></span></li>
								</ul>
							</div>
						</div>
						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
<div class="js-slide" data-arrow="true" data-dots="true" data-interval="6000">
	<ul class="js-slide__ul">
		<li class="js-slide__li"><span class="js-slide__image js-lazy_bgi" data-bgi="<?php echo $local_path; ?>/assets/image/_dummy/pic-dummy.webp"></span></li>
		<li class="js-slide__li"><span class="js-slide__image js-lazy_bgi" data-bgi="<?php echo $local_path; ?>/assets/image/_dummy/pic-dummy_b.webp"></span></li>
		<li class="js-slide__li"><span class="js-slide__image js-lazy_bgi" data-bgi="<?php echo $local_path; ?>/assets/image/_dummy/pic-dummy.webp"></span></li>
		<li class="js-slide__li"><span class="js-slide__image js-lazy_bgi" data-bgi="<?php echo $local_path; ?>/assets/image/_dummy/pic-dummy_b.webp"></span></li>
	</ul>
</div>
]]></math></code></pre>
						</div>
					</div>
				</section>

				<section id="Stream">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ Stream ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">
							<div>
								<div class="js-stream" data-reverse="false">
									<div class="js-stream__wrap">
										<div class="js-stream__inner">
											<span class="js-stream__text">This is dummy copy. It is not meant to be read. </span>
										</div>
									</div>
								</div>
								<br><br>
								<div class="js-stream" data-reverse="true">
									<div class="js-stream__wrap">
										<div class="js-stream__inner">
											<span class="js-stream__text">This is dummy copy. It is not meant to be read. </span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
<div class="js-stream" data-reverse="false">
	<div class="js-stream__wrap">
		<div class="js-stream__inner">
			<span class="js-stream__text">This is dummy copy. It is not meant to be read. </span>
		</div>
	</div>
</div>

<div class="js-stream" data-reverse="true">
	<div class="js-stream__wrap">
		<div class="js-stream__inner">
			<span class="js-stream__text">This is dummy copy. It is not meant to be read. </span>
		</div>
	</div>
</div>
]]></math></code></pre>
						</div>
					</div>
				</section>

				<section id="Map">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ Map ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">
							<div class="js-map" data-lat="35.326968" data-lng="139.436861" data-pin="<?php echo $image_path; ?>common/pin.svg">
								<div class="js-map__wrap"></div>
							</div>
						</div>
						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
<div class="js-map" data-lat="35.326968" data-lng="139.436861" data-pin="<?php echo $image_path; ?>common/pin.svg">
	<div class="js-map__wrap"></div>
</div>
]]></math></code></pre>
						</div>
					</div>
				</section>

				<section id="Accordion">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ Accordion ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">
							<div class="js-accordion">
								<div class="js-accordion__head"><span>open</span></div>
								<div class="js-accordion__body"><span>This text is a dummy. It is included to check the size, amount, spacing, and line spacing of the text.</span></div>
							</div>
						</div>
						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
<div class="js-accordion">
	<div class="js-accordion__head"><span>xxxxxxxxx</span></div>
	<div class="js-accordion__body"><span>xxxxxxxxx</span></div>
</div>
]]></math></code></pre>
						</div>
					</div>
				</section>

				<section id="Tab">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ Tab ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">
							<div class="js-tab">
								<div class="js-tab__wrap">
									<div class="js-tab__head">
										<ul>
											<li><a href="javascript:void(0);" class="js-tab__nav is-active" data-tab="tab1"><span>Tab 01</span></a></li>
											<li><a href="javascript:void(0);" class="js-tab__nav" data-tab="tab2"><span>Tab 02</span></a></li>
											<li><a href="javascript:void(0);" class="js-tab__nav" data-tab="tab3"><span>Tab 03</span></a></li>
											<li><a href="javascript:void(0);" class="js-tab__nav" data-tab="tab4"><span>Tab 04</span></a></li>
										</ul>
									</div>
									<div class="js-tab__body">
										<div class="js-tab__body__wrap">
											<div class="js-tab__content is-active" data-tab="tab1">
												<div class="js-tab__content__wrap">
													<span>Content 01<br>This text is a dummy. It is included to check the size, amount, spacing, and line spacing of the text.</span>
												</div>
											</div>
											<div class="js-tab__content" data-tab="tab2">
												<div class="js-tab__content__wrap">
													<span>Content 02<br>This text is a dummy. It is included to check the size, amount, spacing, and line spacing of the text.</span>
												</div>
											</div>
											<div class="js-tab__content" data-tab="tab3">
												<div class="js-tab__content__wrap">
													<span>Content 03<br>This text is a dummy. It is included to check the size, amount, spacing, and line spacing of the text.This text is a dummy. It is included to check the size, amount, spacing, and line spacing of the text.This text is a dummy. It is included to check the size, amount, spacing, and line spacing of the text.</span>
												</div>
											</div>
											<div class="js-tab__content" data-tab="tab4">
												<div class="js-tab__content__wrap">
													<span>Content 04<br>This text is a dummy. It is included to check the size, amount, spacing, and line spacing of the text.</span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
<div class="js-tab">
	<div class="js-tab__wrap">
		<div class="js-tab__head">
			<ul>
				<li><a href="javascript:void(0);" class="js-tab__nav is-active" data-tab="tab1"><span>Tab 01</span></a></li>
				<li><a href="javascript:void(0);" class="js-tab__nav" data-tab="tab2"><span>Tab 02</span></a></li>
				<li><a href="javascript:void(0);" class="js-tab__nav" data-tab="tab3"><span>Tab 03</span></a></li>
			</ul>
		</div>
		<div class="js-tab__body">
			<div class="js-tab__body__wrap">
				<div class="js-tab__content is-active" data-tab="tab1">
					<div class="js-tab__content__wrap">
						<span>Content 01</span>
					</div>
				</div>
				<div class="js-tab__content" data-tab="tab2">
					<div class="js-tab__content__wrap">
						<span>Content 02</span>
					</div>
				</div>
				<div class="js-tab__content" data-tab="tab3">
					<div class="js-tab__content__wrap">
						<span>Content 03</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
]]></math></code></pre>
						</div>
					</div>
				</section>

				<section id="FloatingBox">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ FloatingBox ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">
							右下に表示

							<div class="js-floating">
								<a href="javascript:void(0);" class="js-floating__close"><span></span></a>
								<div class="js-floating__wrap">
									<?php
									$cv = array(
										"mode" => "link",
										"link" => array(
											"url" => setHtmlTel('012-345-6789'),
											"target" => "",
										),
										// 'icon' => 'icon-tel3',
										// 'title' => '012-345-6789',
										// 'text' => '営業時間 : 平日 9:00 – 17:00',
										'image' => setValueImage($image_path . '_dummy/pic-dummy.webp'),
										'title' => 'お問合せはこちら',
										'text' => 'この文章はダミーです。文字サイズの確認のために入れております。',
									);
									setHtmlCv($cv, "p-cv");
									?>
								</div>
							</div>
						</div>
						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
<div class="js-floating">
	<a href="javascript:void(0);" class="js-floating__close"><span></span></a>
	<a href="javascript:void(0);" target="" class="js-floating__link">
		<span class="js-floating__box">
			<span class="js-floating__box__wrap">
				<span class="js-floating__box__image"><span class="js-lazy_bgi" data-bgi="<?php echo $local_path; ?>/assets/image/_dummy/pic-dummy.webp"></span></span>
				<span class="js-floating__box__detail">
					<span class="p-cap"><span>この文章はダミーです。文字の大きさ、量、字間、行間等を確認するために入れています。</span></span>
				</span>
			</span>
		</span>
	</a>
</div>
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
