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
				<section id="Modal">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ Normal Modal ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">
							<a href="javascript:void(0);" class="p-button js-modal__open" data-modal="modal_01"><span>Modal Open</span></a>
							<div class="js-modal__content" data-modal="modal_01">
								<div class="js-modal__content__wrap">
									<div class="js-modal__content__inner">
										<div class="js-modal__close js-modal__close__head"><span></span></div>
										<div class="js-modal__content__box 任意のクラス" style="width: 90vw; max-width: 50rem; padding: 4rem;">
											<p class="p-text"><span>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。</span></p>
										</div>
										<div class="js-modal__close js-modal__close__bottom"><span><span></span>閉じる</span></div>
									</div>
								</div>
							</div>
							<br><br>
							<a href="javascript:void(0);" class="p-button js-modal__open" data-modal="modal_02"><span>Modal Open</span></a>
							<div class="js-modal__content" data-modal="modal_02">
								<div class="js-modal__content__wrap">
									<div class="js-modal__content__inner">
										<div class="js-modal__close js-modal__close__head"><span></span></div>
										<div class="js-modal__content__box 任意のクラス" style="width: 90vw; max-width: 50rem; padding: 4rem;">
											<p class="p-text"><span>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。<br><br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。<br><br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。<br><br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。<br><br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。<br><br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。<br><br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。<br><br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。</span></p>
										</div>
										<div class="js-modal__close js-modal__close__bottom"><span><span></span>閉じる</span></div>
									</div>
								</div>
							</div>
						</div>

						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
//[Button]
<a href="javascript:void(0);" class="js-modal__open" data-modal="ModalName"><span>Modal Open</span></a>

//[Content]
<div class="js-modal__content" data-modal="ModalName">
	<div class="js-modal__content__wrap">
		<div class="js-modal__content__inner">
			<div class="js-modal__close js-modal__close__head"><span></span></div>
			<div class="js-modal__content__box 任意のクラス">
				<!-- FREE HTML -->
			</div>
			<div class="js-modal__close js-modal__close__bottom"><span><span></span>閉じる</span></div>
		</div>
	</div>
</div>

//CSS
.任意のクラス{
	width: 90vw;
	max-width: 50rem;
	padding: 4rem;
}
]]></math></code></pre>
						</div>
					</div>
				</section>



				<section id="Modal">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ Normal Modal ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">
							<a href="javascript:void(0);" class="p-button js-modal__open" data-modal="modal_03"><span>Modal Open</span></a>
							<div class="js-modal__content" data-modal="modal_03">
								<div class="js-modal__content__wrap">
									<div class="js-modal__content__inner">
										<div class="js-modal__close js-modal__close__head"><span></span></div>
										<div class="js-modal__content__box 任意のクラス" style="width: 90vw; max-width: 50rem; padding: 4rem; max-height: 50vh; overflow: auto;">
											<p class="p-text"><span>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。<br><br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。<br><br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。<br><br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。<br><br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。<br><br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。<br><br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。<br><br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。</span></p>
										</div>
										<div class="js-modal__close js-modal__close__bottom"><span><span></span>閉じる</span></div>
									</div>
								</div>
							</div>
						</div>
						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
//CSS
.任意のクラス{
	width: 90vw;
	max-width: 50rem;
	padding: 4rem;
	max-height: 50vh;
	overflow: auto;
}
]]></math></code></pre>
						</div>
					</div>
				</section>

				<section id="ModalImage">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ Image Only ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">
							<a href="javascript:void(0);" class="p-button js-modal__open" data-modal="modal_04_01"><span>Modal Open</span></a>
							<br><br>
							<a href="javascript:void(0);" class="p-button js-modal__open" data-modal="modal_04_02"><span>Modal Open</span></a>

							<div class="js-modal__content" data-modal="modal_04_01">
								<div class="js-modal__close__outer"></div>
								<div class="js-modal__content__image">
									<div class="js-modal__content__image__wrap">
										<img src="<?php echo $local_path; ?>/assets/image/common/space.webp" data-src="<?php echo $local_path; ?>/assets/image/_dummy/pic-dummy_image2.webp">
									</div>
								</div>
							</div>

							<div class="js-modal__content" data-modal="modal_04_02">
								<div class="js-modal__close__outer"></div>
								<div class="js-modal__content__image">
									<div class="js-modal__content__image__wrap">
										<img src="<?php echo $local_path; ?>/assets/image/common/space.webp" data-src="<?php echo $local_path; ?>/assets/image/_dummy/pic-dummy_image.webp">
									</div>
								</div>
							</div>
						</div>
						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
//[Button]
<a href="javascript:void(0);" class="js-modal__open" data-modal="ModalName"><span>Modal Open</span></a>

//[Content]
<div class="js-modal__content" data-modal="ModalName">
	<div class="js-modal__close__outer"></div>
	<div class="js-modal__content__image">
		<div class="js-modal__content__image__wrap">
			<img src="<php echo $local_path;>/assets/image/common/space.webp" data-src="IMAGE PATH">
		</div>
	</div>
</div>

// 画像の場合は遅延読み込みが必要なので
// <img src="<php echo $local_path;>/assets/image/common/space.webp" data-src="IMAGE PATH">
]]></math></code></pre>
						</div>
					</div>
				</section>

				<section id="ModalYoutube">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ Youtube Only ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">
							<a href="javascript:void(0);" class="p-button js-modal__open" data-modal="modal_05"><span>Modal Open</span></a>

							<div class="js-modal__content" data-modal="modal_05">
								<div class="js-modal__close__outer"></div>
								<div class="js-modal__content__youtube">
									<div class="js-modal__content__youtube__wrap">
										<div class="js-youtube" data-id="1TH0LCSzFng"></div>
									</div>
									<div class="js-modal__close"></div>
								</div>
							</div>

						</div>
						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
//[Button]
<a href="javascript:void(0);" class="js-modal__open" data-modal="ModalName"><span>Modal Open</span></a>

//[Content]
<div class="js-modal__content" data-modal="ModalName">
	<div class="js-modal__close__outer"></div>
	<div class="js-modal__content__youtube">
		<div class="js-modal__content__youtube__wrap">
			<div class="js-youtube" data-id="YOUTUBE ID"></div>
		</div>
		<div class="js-modal__close"></div>
	</div>
</div>

// 画像の場合は遅延読み込みが必要なので
// <img src="<php echo $local_path;>/assets/image/common/space.webp" data-src="IMAGE PATH">
]]></math></code></pre>
						</div>
					</div>
				</section>

				<section id="ModalImageText">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ Image Text ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">

							<a href="javascript:void(0);" class="p-button js-modal__open" data-modal="modal_06_01"><span>Modal Open</span></a>
							<br><br>
							<a href="javascript:void(0);" class="p-button js-modal__open" data-modal="modal_06_02"><span>Modal Open</span></a>
							<div class="js-modal__content" data-modal="modal_06_01">
								<div class="js-modal__close__outer"></div>
								<div class="js-modal__content__fl">
									<div class="js-modal__content__fl__wrap">
										<div class="js-modal__content__fl__image">
											<div class="js-modal__content__fl__image__wrap">
												<img src="<?php echo $local_path; ?>/assets/image/common/space.webp" data-src="<?php echo $local_path; ?>/assets/image/_dummy/pic-dummy_image.webp">
											</div>
										</div>
										<div class="js-modal__content__fl__detail">
											<div class="js-modal__content__fl__detail__wrap">
												<p class="p-text" style="color: #ffffff;"><span>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。</span></p><br><br>
												<div class="js-modal__close js-modal__close__bottom"><span><span></span>閉じる</span></div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="js-modal__content" data-modal="modal_06_02">
								<div class="js-modal__close__outer"></div>
								<div class="js-modal__content__fl">
									<div class="js-modal__content__fl__wrap">
										<div class="js-modal__content__fl__image">
											<div class="js-modal__content__fl__image__wrap">
												<img src="<?php echo $local_path; ?>/assets/image/common/space.webp" data-src="<?php echo $local_path; ?>/assets/image/_dummy/pic-dummy_image2.webp">
											</div>
										</div>
										<div class="js-modal__content__fl__detail">
											<div class="js-modal__content__fl__detail__wrap">
												<p class="p-text" style="color: #ffffff;"><span>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。</span></p><br><br>
												<div class="js-modal__close js-modal__close__bottom"><span><span></span>閉じる</span></div>
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>
						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
//[Button]
<a href="javascript:void(0);" class="js-modal__open" data-modal="ModalName"><span>Modal Open</span></a>

//[Content]
<div class="js-modal__content" data-modal="ModalName">
	<div class="js-modal__close__outer"></div>
	<div class="js-modal__content__fl">
		<div class="js-modal__content__fl__wrap">
			<div class="js-modal__content__fl__image">
				<div class="js-modal__content__fl__image__wrap">
					<img src="<php echo $local_path;>/assets/image/common/space.webp" data-src="IMAGE PATH">
				</div>
			</div>
			<div class="js-modal__content__fl__detail">
				<div class="js-modal__content__fl__detail__wrap">
					<!-- FREE HTML -->
					<div class="js-modal__close js-modal__close__bottom"><span><span></span>閉じる</span></div>
				</div>
			</div>
		</div>
	</div>
</div>

// 画像の場合は遅延読み込みが必要なので
// <img src="<php echo $local_path;>/assets/image/common/space.webp" data-src="IMAGE PATH">
]]></math></code></pre>
						</div>
					</div>
				</section>

				<section id="ModalYoutubeText">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ Youtube Text ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">

							<a href="javascript:void(0);" class="p-button js-modal__open" data-modal="modal_07"><span>Modal Open</span></a>
							<div class="js-modal__content" data-modal="modal_07">
								<div class="js-modal__close__outer"></div>
								<div class="js-modal__content__fl">
									<div class="js-modal__content__fl__wrap">
										<div class="js-modal__content__fl__youtube">
											<div class="js-modal__content__fl__youtube__wrap">
												<div class="js-youtube" data-id="1TH0LCSzFng"></div>
											</div>
										</div>
										<div class="js-modal__content__fl__detail">
											<div class="js-modal__content__fl__detail__wrap">
												<p class="p-text" style="color: #ffffff;"><span>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。</span></p><br><br>
												<div class="js-modal__close js-modal__close__bottom"><span><span></span>閉じる</span></div>
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>
						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
//[Button]
<a href="javascript:void(0);" class="js-modal__open" data-modal="ModalName"><span>Modal Open</span></a>

//[Content]
<div class="js-modal__content" data-modal="ModalName">
	<div class="js-modal__close__outer"></div>
	<div class="js-modal__content__fl">
		<div class="js-modal__content__fl__wrap">
			<div class="js-modal__content__fl__youtube">
				<div class="js-modal__content__fl__youtube__wrap">
					<div class="js-youtube" data-id="YOUTUBE ID"></div>
				</div>
			</div>
			<div class="js-modal__content__fl__detail">
				<div class="js-modal__content__fl__detail__wrap">
					<!-- FREE HTML -->
					<div class="js-modal__close js-modal__close__bottom"><span><span></span>閉じる</span></div>
				</div>
			</div>
		</div>
	</div>
</div>

// 画像の場合は遅延読み込みが必要なので
// <img src="<php echo $local_path;>/assets/image/common/space.webp" data-src="IMAGE PATH">
]]></math></code></pre>
						</div>
					</div>
				</section>

				<section id="ModalGallery">
					<div class="<?php echo $TemplateClass; ?>__wrap">
						<div class="<?php echo $TemplateClass; ?>__head">
							<span class="<?php echo $TemplateClass; ?>__title">[ Gallery ]</span>
						</div>
						<div class="<?php echo $TemplateClass; ?>__body">
							<a href="javascript:void(0);" class="p-button js-modal__open" data-modal="modal_08" data-index="0"><span>Modal Open Gallery 01</span></a>
							<br><br>
							<a href="javascript:void(0);" class="p-button js-modal__open" data-modal="modal_08" data-index="1"><span>Modal Open Gallery 02</span></a>
							<div class="js-modal__content" data-modal="modal_08" data-index="0">
								<div class="js-modal__close__outer"></div>
								<div class="js-modal__content__fl">
									<div class="js-modal__content__fl__wrap">
										<div class="js-modal__content__fl__image">
											<div class="js-modal__content__fl__image__wrap">
												<img src="<?php echo $local_path; ?>/assets/image/common/space.webp" data-src="<?php echo $local_path; ?>/assets/image/_dummy/pic-dummy_image.webp">
											</div>
										</div>
										<div class="js-modal__content__fl__detail">
											<div class="js-modal__content__fl__detail__wrap">
												<p class="p-text" style="color: #ffffff;"><span>【 Gallery 01 】<br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。</span></p>
												<div class="js-modal__ctrl"></div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="js-modal__content" data-modal="modal_08" data-index="1">
								<div class="js-modal__close__outer"></div>
								<div class="js-modal__content__fl">
									<div class="js-modal__content__fl__wrap">
										<div class="js-modal__content__fl__youtube">
											<div class="js-modal__content__fl__youtube__wrap">
												<div class="js-youtube" data-id="1TH0LCSzFng"></div>
											</div>
										</div>
										<div class="js-modal__content__fl__detail">
											<div class="js-modal__content__fl__detail__wrap">
												<p class="p-text" style="color: #ffffff;"><span>【 Gallery 02 】<br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした事がある。なぜそんな無闇をしたと聞く人があるかも知れぬ。別段深い理由でもない。新築の二階から首を出していたら、同級生の一人が冗談に、いくら威張っても、そこから飛び降りる事は出来まい。弱虫やーい。と囃したからである。</span></p>
												<div class="js-modal__ctrl"></div>
											</div>
										</div>
									</div>
								</div>
							</div>


						</div>

						<div class="<?php echo $TemplateClass; ?>__foot">
							<pre><code><math><![CDATA[
//[Button]
<a href="javascript:void(0);" class="js-modal__open" data-modal="ModalName" data-index="0"><span>Modal Open Gallery 01</span></a>
<a href="javascript:void(0);" class="js-modal__open" data-modal="ModalName" data-index="1"><span>Modal Open Gallery 02</span></a>

//[Content]
<div class="js-modal__content" data-modal="ModalName" data-index="0">
	<div class="js-modal__close__outer"></div>
	<div class="js-modal__content__fl">
		<div class="js-modal__content__fl__wrap">
			<div class="js-modal__content__fl__image">
				<div class="js-modal__content__fl__image__wrap">
					<img src="<php echo $local_path;>/assets/image/common/space.webp" data-src="IMAGE PATH">
				</div>
			</div>
			<div class="js-modal__content__fl__detail">
				<div class="js-modal__content__fl__detail__wrap">
					<!-- FREE HTML -->
					<div class="js-modal__ctrl"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="js-modal__content" data-modal="ModalName" data-index="1">
	<div class="js-modal__close__outer"></div>
	<div class="js-modal__content__fl">
		<div class="js-modal__content__fl__wrap">
			<div class="js-modal__content__fl__youtube">
				<div class="js-modal__content__fl__youtube__wrap">
					<div class="js-youtube" data-id="YOUTUBE ID"></div>
				</div>
			</div>
			<div class="js-modal__content__fl__detail">
				<div class="js-modal__content__fl__detail__wrap">
					<!-- FREE HTML -->
					<div class="js-modal__ctrl"></div>
				</div>
			</div>
		</div>
	</div>
</div>

// 同一のdata-modal="ModalName"でグループ化
// data-index="n"で順番（コンテンツとボタン両方に入れる）
// 画像の場合は遅延読み込みが必要なので
// <img src="<php echo $local_path;>/assets/image/common/space.webp" data-src="IMAGE PATH">


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
