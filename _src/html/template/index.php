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
			<?php $TemplateClass = 'b-template'; ?>
			<div class="<?php echo $TemplateClass; ?>">

				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">LOGO</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-logo">
										<li>
											<dl>
												<dt><?php setHtmlLogo("");  ?></dt>
												<dd>.p-logo</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>
				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">COLOR</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<?php
								$Array = array(
									array('--Title', '--Text', '--SubText', '--Border'),
									array('--Key1', '--Key2', '--Key3', '--Attention'),
									array('--Base1', '--Base2', '--Base3', '--White'),
								);
								?>
								<?php foreach ($Array as $colorArray) : ?>
									<div class="<?php echo $TemplateClass; ?>__content">
										<ul class="<?php echo $TemplateClass; ?>__list is-color">
											<?php foreach ($colorArray as $color) : ?>
												<li>
													<dl>
														<dt>
															<div class="swatch" style="background-color: var(<?php echo $color ?>);"></div>
														</dt>
														<dd>
															<?php echo $color ?><br>
															<?php echo cssVars($color); ?>
														</dd>
													</dl>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</section>



				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">FONT</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<?php
								$Array = array(
									array(
										'var' => '--jp_go',
										'title' => 'あア漢',
										'text' => 'この文章はダミーです。文字の大きさ、量、字間、行間等を確認するために入れています。この文章はダミーです。'
									),
									array(
										'var' => '--en_go',
										'title' => 'Aa1',
										'text' => 'This text is a dummy. It is included to check the size, amount, spacing, and line spacing of the text.'
									),
									array(
										'var' => '--go',
										'title' => 'Aあaア1円',
										'text' => 'Google の無料サービスなら、単語、フレーズ、ウェブページを英語から 100 以上の他言語にすぐに翻訳できます。'
									),
									array(
										'var' => '--jp_min',
										'title' => 'あア漢',
										'text' => 'この文章はダミーです。文字の大きさ、量、字間、行間等を確認するために入れています。この文章はダミーです。'
									),
									array(
										'var' => '--en_min',
										'title' => 'Aa1',
										'text' => 'This text is a dummy. It is included to check the size, amount, spacing, and line spacing of the text.'
									),
									array(
										'var' => '--min',
										'title' => 'Aあaア1円',
										'text' => 'Google の無料サービスなら、単語、フレーズ、ウェブページを英語から 100 以上の他言語にすぐに翻訳できます。'
									),
								);
								?>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-font">
										<?php foreach ($Array as $font) : ?>
											<li>
												<dl>
													<dt>
														<p class="title" style="font-family: var(<?php echo $font['var']; ?>);"><?php echo $font['title']; ?></p>
														<p class="text" style="font-family: var(<?php echo $font['var']; ?>);"><?php echo $font['text']; ?></p>
													</dt>
													<dd>
														<?php echo $font['var']; ?>
														<br>
														<?php if ($font['var'] == '--go') : ?>
															var(--en_go), var((--jp_go)
														<?php elseif ($font['var'] == '--min') : ?>
															var(--en_min), var(--jp_min)
														<?php else : ?>
															<?php
															$parts = explode(",", cssVars($font['var']));
															echo $parts[0];
															echo $parts[1];
															?>
														<?php endif; ?>

													</dd>
												</dl>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>


				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">TITLE</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt>
													<?php
													$title = array('main' => 'ページ見出し', 'sub' => 'Page Title');
													setHtmlTitle($title, 'p-title__page', 'h1');
													?>
												</dt>
												<dd>.p-title__page</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$title = array('main' => 'セクション見出し', 'sub' => 'Section Title');
													setHtmlTitle($title, 'p-title__sec', 'h2');
													?>
												</dt>
												<dd>.p-title__sec</dd>
											</dl>
										</li>

										<li>
											<dl>
												<dt>
													<?php
													$title = array('main' => 'サブ見出し', 'sub' => 'sub title');
													setHtmlTitle($title, 'p-title__sub', 'h3');
													?>
												</dt>
												<dd>.p-title__sub (改行なし・左寄せ)</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$title = array('main' => 'この文章はダミーです。文字サイズと量・字間・行間等を<br>確認のために入れており、ここまでが50文字です。', 'sub' => 'sub title');
													setHtmlTitle($title, 'p-title__sub is-text', 'h4');
													?>
												</dt>
												<dd>.p-title__sub + .is-text (改行あり・左寄せ)</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$title = array('main' => 'サブ見出し', 'sub' => 'sub title');
													setHtmlTitle($title, 'p-title__sub2', 'h3');
													?>
												</dt>
												<dd>.p-title__sub2</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$title = array('main' => 'サブ見出し', 'sub' => 'sub title');
													setHtmlTitle($title, 'p-title__num', 'h3');
													?>
												</dt>
												<dd>.p-title__num</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$title = array('main' => $dummy_text, 'sub' => 'この文章はダミーです。');
													setHtmlTitle($title, 'p-title__box', 'h4');
													?>
												</dt>
												<dd>.p-title__box</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>


				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">TEXT</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<?php
								$Array = array(
									array(
										'class' => 'p-lead',
										'text' => 'あのイーハトーヴォのすきとおった風、夏でも底に冷たさをもつ青いそら、うつくしい森で飾られたモリーオ市、郊外のぎらぎらひかる草の波。'
									),
									array(
										'class' => 'p-lead__s',
										'text' => 'あのイーハトーヴォのすきとおった風、夏でも底に冷たさをもつ青いそら、うつくしい森で飾られたモリーオ市、郊外のぎらぎらひかる草の波。'
									),
									array(
										'class' => 'p-text',
										'text' => 'あのイーハトーヴォのすきとおった風、夏でも底に冷たさをもつ青いそら、うつくしい森で飾られたモリーオ市、郊外のぎらぎらひかる草の波。あのイーハトーヴォのすきとおった風、夏でも底に冷たさをもつ青いそら、うつくしい森で飾られたモリーオ市、郊外のぎらぎらひかる草の波。あのイーハトーヴォのすきとおった風、夏でも底に冷たさをもつ青いそら、うつくしい森で飾られたモリーオ市、郊外のぎらぎらひかる草の波。'
									),
									array(
										'class' => 'p-caption',
										'text' => 'あのイーハトーヴォのすきとおった風、夏でも底に冷たさをもつ青いそら、うつくしい森で飾られたモリーオ市、郊外のぎらぎらひかる草の波。'
									),
									array(
										'class' => 'p-date',
										'text' => '2024.01.15'
									),
									array(
										'class' => 'p-copyright',
										'text' => $copyright
									),
								);
								?>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<?php foreach ($Array as $text) : ?>
											<li>
												<dl>
													<dt>
														<?php setHtmlText($text['text'], $text['class']); ?>
													</dt>
													<dd>
														.<?php echo $text['class']; ?>
													</dd>
												</dl>
											</li>
										<?php endforeach; ?>
										<li>
											<dl>
												<dt>
													<?php setHtmlInfo(); ?>
												</dt>
												<dd>
													.p-info
												</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$body = array(
														'date' => "制定日：2022年11月12日<br>改訂日：2022年11月12日<br>",
														'title' => "代表取締役",
														'people' => "山田太郎",
													);
													setHtmlSign($body, "p-sign"); ?>
												</dt>
												<dd>
													.p-sign
												</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>

				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">TEXT LIST</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<?php
								$body = array(
									array('text' => $dummy_text . $dummy_text . $dummy_text,),
									array('text' => $dummy_text . $dummy_text . $dummy_text,),
									array('text' => $dummy_text . $dummy_text . $dummy_text,),
								);
								?>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt>
													<?php setHtmlUl($body, "p-attention"); ?>
												</dt>
												<dd>.p-attention</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php setHtmlUl($body, "p-ul"); ?>
												</dt>
												<dd>.p-ul</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php setHtmlUl($body, "p-ol"); ?>
												</dt>
												<dd>.p-ol</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>

				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">BUTTON</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<?php
								$button = setValueLink($link_path . '#', 'View Detail', '_blank');
								$button2 =  array(
									'mode' => "link",
									'link' => array(
										'url' => $link_path . '#',
										'title' => "Back",
										'target' =>  "_self",
									),
								);
								$button3 =  array(
									'mode' => "link",
									'link' => array(
										'url' => $link_path . '#',
										'title' => "External Links",
										'target' =>  "_blank",
									),
								);
								$button4 =  array(
									'mode' => "link",
									'link' => array(
										'url' => "mailto:a@delaunay.jp",
										'title' => "Email",
										'target' =>  "",
									),
								);
								?>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt><?php setHtmlLink($button, "p-button", "icon-arrow"); ?></dt>
												<dd>.p-button</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlLink($button2, "p-button", "icon-back"); ?></dt>
												<dd>.p-button</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlLink($button3, "p-button", "icon-back"); ?></dt>
												<dd>.p-button [target="_blank"]</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlLink($button, "p-button is-size__small", "icon-arrow"); ?></dt>
												<dd>.p-button + .is-size__small</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlLink($button, "p-button is-size__large", "icon-arrow"); ?></dt>
												<dd>.p-button + .is-size__large</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<div class="p-button">
														<input type="submit" name="submitConfirm" value="送信する">
													</div>
												</dt>
												<dd>.p-button > input</dd>
											</dl>
										</li>
									</ul>
								</div>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list">
										<li>
											<dl>
												<dt><?php setHtmlLink($button, "p-button is-color__reverse", "icon-arrow"); ?></dt>
												<dd>.p-button + .is-color__reverse</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlLink($button, "p-button is-color__sub", "icon-arrow"); ?></dt>
												<dd>.p-button + .is-color__sub</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlLink($button, "p-button is-color__sub is-color__reverse", "icon-arrow"); ?></dt>
												<dd>.p-button + .is-color__sub + .is-color__reverse</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlLink($button, "p-button is-color__white", "icon-arrow"); ?></dt>
												<dd>.p-button + .is-color__white</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlLink($button, "p-button is-color__white is-color__reverse", "icon-arrow"); ?></dt>
												<dd>.p-button + .is-color__white + .is-color__reverse</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlLink($button, "p-button is-color__disable", "icon-arrow"); ?></dt>
												<dd>.p-button + .is-color__disable</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlLink($button4, "p-button"); ?></dt>
												<dd>.p-button / email</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>



				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">CONVERSION</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<?php
								$cv = array(
									"mode" => "link",
									"link" => array(
										"url" => setHtmlTel('012-345-6789'),
										"target" => "",
									),
									'icon' => 'icon-tel3',
									'title' => '012-345-6789',
									'text' => '営業時間 : 平日 9:00 – 17:00',
								);

								$cv2 = array(
									"mode" => "link",
									"link" => array(
										"url" => setHtmlTel('012-345-6789'),
										"target" => "",
									),
									'icon' => 'icon-file',
									'title' => 'まずは資料請求',
									'text' => 'お悩みの方はコチラから',
								);
								$cv3 = array(
									"mode" => "link",
									"link" => array(
										"url" => setHtmlTel('012-345-6789'),
										"target" => "",
									),
									'icon' => 'icon-mail',
									'title' => 'お問合せはこちら',
									'text' => 'お気軽にご相談ください',
								);
								$cv4 = array(
									"mode" => "link",
									"link" => array(
										"url" => setHtmlTel('012-345-6789'),
										"target" => "",
									),
									'image' => setValueImage($image_path . '_dummy/pic-dummy.webp'),
									'title' => '012-345-6789',
									'text' => '営業時間 : 平日 9:00 – 17:00',
								);
								$cv5 = array(
									"mode" => "link",
									"link" => array(
										"url" => setHtmlTel('012-345-6789'),
										"target" => "",
									),
									'image' => setValueImage($image_path . '_dummy/pic-dummy.webp'),
									'title' => 'お問合せはこちら',
									'text' => 'この文章はダミーです。文字サイズの確認のために入れております。',
								);
								?>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt>
													<?php setHtmlCv($cv, "p-cv"); ?>
												</dt>
												<dd>.p-cv</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php setHtmlCv($cv2, "p-cv"); ?>
												</dt>
												<dd>.p-cv</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php setHtmlCv($cv3, "p-cv"); ?>
												</dt>
												<dd>.p-cv</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php setHtmlCv($cv, "p-cv is-size__large"); ?>
												</dt>
												<dd>.p-cv + .is-size__large</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php setHtmlCv($cv4, "p-cv"); ?>
												</dt>
												<dd>.p-cv / image</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php setHtmlCv($cv5, "p-cv"); ?>
												</dt>
												<dd>.p-cv / image</dd>
											</dl>
										</li>
									</ul>
								</div>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list">
										<li>
											<dl>
												<dt>
													<?php setHtmlCv($cv, "p-cv is-color__reverse"); ?>
												</dt>
												<dd>.p-cv + is-color__reverse</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php setHtmlCv($cv, "p-cv is-color__sub"); ?>

												</dt>
												<dd>.p-cv + is-color__sub</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php setHtmlCv($cv, "p-cv is-color__sub is-color__reverse"); ?>
												</dt>
												<dd>.p-cv + is-color__sub + .is-color__reverse</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php setHtmlCv($cv, "p-cv is-color__white"); ?>

												</dt>
												<dd>.p-cv + is-color__white</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php setHtmlCv($cv, "p-cv is-color__white is-color__reverse"); ?>
												</dt>
												<dd>.p-cv + is-color__white + .is-color__reverse</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php setHtmlCv($cv, "p-cv is-color__disable"); ?>
												</dt>
												<dd>.p-cv + is-color__disable</dd>
											</dl>
										</li>

									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>




				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">LINK</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list">
										<li>
											<dl>
												<dt><?php setHtmlLink($button, "p-link is-text", "icon-arrow"); ?></dt>
												<dd>.p-link</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlLink($button2, "p-link is-text", "icon-back"); ?></dt>
												<dd>.p-link</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlLink($button3, "p-link is-text is-color__sub", "icon-back"); ?></dt>
												<dd>.p-link + .is-text + .is-color__sub [target="_blank"]</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlLink($button3, "p-link is-text is-color__white", "icon-back"); ?></dt>
												<dd>.p-link + .is-text + .is-color__white</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>

				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">TAG</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<?php
								$body = setValueTaxonmy($p_key, '_category', array(
									array('メディア紹介', 'category1'),
									array('お知らせ', 'category2'),
									array('プレスリリース', 'category3'),
								));
								?>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt>
													<?php
													setHtmlTaxonomy($body, "p-category", true);
													?>
												</dt>
												<dd>.p-category</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													setHtmlTaxonomy($body, "p-tag", false);
													?>
												</dt>
												<dd>.p-tag</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>

				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">PANKUZU</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<?php
								$body = array(
									'url' => '/news/',
									'target' => '',
									'title' => array('main' => 'お知らせ', 'sub' => 'News', 'icon' => '',),
									'icon' => '',
									'class' => '',
									'parent' => 'home',
									'display' => array(0 => 'header', 1 => 'modal', 2 => 'modal1', 3 => 'footer1',),
								);
								?>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt>
													<?php
													setHtmlPankuzu($body);
													?>
												</dt>
												<dd>.p-category</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>

				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">IMAGE</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<?php $image = setValueImage($image_path . '_dummy/pic-dummy.webp'); ?>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list">
										<li>
											<dl>
												<dt><?php setHtmlBgImage($image, "p-image"); ?></dt>
												<dd>.p-image<br>3:2</dd>
											</dl>
										</li>

										<li>
											<dl>
												<dt><?php setHtmlBgImage($image, "p-image__r50"); ?></dt>
												<dd>.p-image__r50<br>2:1</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlBgImage($image, "p-image__r56"); ?></dt>
												<dd>.p-image__r56<br>16:9</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlBgImage($image, "p-image__r66"); ?></dt>
												<dd>.p-image__r66<br>3:2</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlBgImage($image, "p-image__r75"); ?></dt>
												<dd>.p-image__r75<br>4:3</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlBgImage($image, "p-image__r100"); ?></dt>
												<dd>.p-image__r100<br>1:1</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlBgImage($image, "p-image__r120"); ?></dt>
												<dd>.p-image__r120<br>3:4</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlBgImage($image, "p-image__r150"); ?></dt>
												<dd>.p-image__r150<br>2:3</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlBgImage($image, "p-image__r177"); ?></dt>
												<dd>.p-image__r177<br>9:16</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlBgImage($image, "p-image__r200"); ?></dt>
												<dd>.p-image__r200<br>1:2</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlBgImage($image, "p-image is-link"); ?></dt>
												<dd>.p-image + .is-link</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlBgImage($image, "p-image is-play"); ?></dt>
												<dd>.p-image + .is-play</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>

				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">MOVIE</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<?php
								$youtube = array(
									'id' => 'aqa9h-nL-TA',
									'image' => setValueImage($image_path . '_dummy/pic-dummy.webp'),
								);
								$movie = array(
									'video' => $local_path . '/assets/files/dummy.mp4',
									'image' => setValueImage($image_path . '_dummy/pic-dummy.webp'),
								);
								?>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list">
										<li>
											<dl>
												<dt><?php setHtmlYoutube($youtube, "p-youtube", 'data-controls="true" data-mute="true" data-loop="false" data-auto="false" data-ratio="56.25"'); ?></dt>
												<dd>.p-youtube</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlVideo($movie, "p-video", "autoplay loop muted playsinline"); ?></dt>
												<dd>.p-movie</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>


				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">ICON</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<?php
								$xml = $root_path . "/assets/image/icon/sprite.svg";
								$xmlData = simplexml_load_file($xml);
								?>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-icon">
										<?php foreach ($xmlData as $icon) : ?>
											<?php if (strpos($icon['id'], "icon") !== false) : ?>
												<li>
													<dl>
														<dt><?php setHtmlSvg($icon['id']); ?></dt>
														<dd><?php echo $icon['id']; ?></dd>
													</dl>
												</li>
											<?php endif; ?>
										<?php endforeach; ?>
									</ul>
								</div>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-icon">
										<?php foreach ($xmlData as $icon) : ?>
											<?php if (strpos($icon['id'], "sns") !== false) : ?>
												<li>
													<dl>
														<dt><?php setHtmlSvg($icon['id']); ?></dt>
														<dd><?php echo $icon['id']; ?></dd>
													</dl>
												</li>
											<?php endif; ?>
										<?php endforeach; ?>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>


				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">TABLE</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<?php
								$th =  '項目名称';
								$td =  'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、<a href="#">リンクはこちら</a>です。';
								$true =  '<span class="true">有</span>';
								$false =  '<span class="false">無</span>';
								$short =  'この文章はダミーです。<br><a href="#">リンクはこちら</a>です。';
								?>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-block">
										<li>
											<dl>
												<dt>
													<div class="p-table">
														<table>
															<tbody>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
															</tbody>
														</table>
													</div>
												</dt>
												<dd>.p-table</dd>
											</dl>
										</li>


										<li>
											<dl>
												<dt>
													<div class="p-table">
														<table style="border-left: none;">
															<tbody>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<td style="border-right: none;"><span><?php echo $td; ?></span></td>
																</tr>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<td style="border-right: none;"><span><?php echo $td; ?></span></td>
																</tr>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<td style="border-right: none;"><span><?php echo $td; ?></span></td>
																</tr>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<td style="border-right: none;"><span><?php echo $td; ?></span></td>
																</tr>
															</tbody>
														</table>
													</div>
												</dt>
												<dd>.p-table + custom</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<div class="p-table">
														<table>
															<thead>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<th><span><?php echo $th; ?></span></th>
																	<th><span><?php echo $th; ?></span></th>
																	<th><span><?php echo $th; ?></span></th>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
															</tbody>
														</table>
													</div>
												</dt>
												<dd>.p-table</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<div class="p-table">
														<table style="border-left: none;">
															<thead>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<th><span><?php echo $th; ?></span></th>
																	<th><span><?php echo $th; ?></span></th>
																	<th style="border-right: none;"><span><?php echo $th; ?></span></th>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																	<td style="border-right: none;"><span><?php echo $td; ?></span></td>
																</tr>
															</tbody>
														</table>
													</div>
												</dt>
												<dd>.p-table + custom</dd>
											</dl>
										</li>

										<li>
											<dl>
												<dt>
													<div class="p-table is-round">
														<table>
															<thead>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<th><span><?php echo $th; ?></span></th>
																	<th><span><?php echo $th; ?></span></th>
																	<th><span><?php echo $th; ?></span></th>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
															</tbody>
														</table>
													</div>
												</dt>
												<dd>.p-table + .is-round</dd>
											</dl>
										</li>

										<li>
											<dl>
												<dt>
													<div class="p-table is-stripes__horizontal">
														<table>
															<thead>
																<tr>
																	<td class="corner"><span></span></td>
																	<th><span><?php echo $th; ?></span></th>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
															</tbody>
														</table>
													</div>
												</dt>
												<dd>.p-table + .is-stripes__horizontal</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<div class="p-table is-stripes__horizontal">
														<table>
															<thead>
																<tr>
																	<td class="is-color__key1 corner"><span></span></td>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
															</tbody>
														</table>
													</div>
												</dt>
												<dd>.p-table + .is-stripes__horizontal<br>thead.is-color__key1</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<div class="p-table is-stripes__vertical">
														<table>
															<thead>
																<tr>
																	<td class="is-color__key1 corner"><span></span></td>
																	<th><span><?php echo $th; ?></span></th>
																	<th><span><?php echo $th; ?></span></th>
																	<th><span><?php echo $th; ?></span></th>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
																<tr>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
																<tr>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
															</tbody>
														</table>
													</div>
												</dt>
												<dd>.p-table + .is-stripes__vertical<br>tbody th.is-color__key1</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<div class="p-table is-stripes__vertical">
														<table>
															<thead>
																<tr>
																	<td class="is-color__key1 corner"><span></span></td>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<th class="is-color__key2"><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
																<tr>
																	<th class="is-color__key2"><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
																<tr>
																	<th class="is-color__key2"><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																	<td><span><?php echo $td; ?></span></td>
																</tr>
															</tbody>
														</table>
													</div>
												</dt>
												<dd>.p-table + .is-stripes__vertical<br>thead.is-color__key1<br>tbody th.is-color__key2</dd>
											</dl>
										</li>

										<li>
											<dl>
												<dt>
													<div class="p-table is-middle is-scroll">
														<table>
															<thead>
																<tr>
																	<td class="is-color__key1 corner"><span></span></td>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																	<th class="is-color__key1" colspan="2"><span><?php echo $th; ?></span></th>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<th rowspan="2"><span><?php echo $th; ?></span></th>
																	<td rowspan="2"><span><?php echo $true; ?></span></td>
																	<td rowspan="2"><span><?php echo $true; ?></span></td>
																	<td rowspan="2"><span><?php echo $false; ?></span></td>
																	<td rowspan="2"><span><?php echo $false; ?></span></td>
																	<td rowspan="2"><span><?php echo $false; ?></span></td>
																	<td rowspan="2"><span><?php echo $false; ?></span></td>
																	<td rowspan="2"><span><?php echo $true; ?></span></td>
																	<td rowspan="2"><span><?php echo $true; ?></span></td>
																	<td class="col row"><span><?php echo $true; ?></span></td>
																	<td class="row"><span><?php echo $true; ?></span></td>
																</tr>
																<tr>
																	<td class="col"><span><?php echo $true; ?></span></td>
																	<td><span><?php echo $true; ?></span></td>
																</tr>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $true; ?></span></td>
																	<td><span><?php echo $true; ?></span></td>
																	<td><span><?php echo $true; ?></span></td>
																	<td><span><?php echo $true; ?></span></td>
																	<td><span><?php echo $false; ?></span></td>
																	<td><span><?php echo $false; ?></span></td>
																	<td><span><?php echo $true; ?></span></td>
																	<td><span><?php echo $true; ?></span></td>
																	<td colspan="2"><span><?php echo $false; ?></span></td>
																</tr>
																<tr>
																	<th><span><?php echo $th; ?></span></th>
																	<td><span><?php echo $true; ?></span></td>
																	<td><span><?php echo $true; ?></span></td>
																	<td><span><?php echo $false; ?></span></td>
																	<td><span><?php echo $false; ?></span></td>
																	<td><span><?php echo $false; ?></span></td>
																	<td><span><?php echo $false; ?></span></td>
																	<td><span><?php echo $true; ?></span></td>
																	<td><span><?php echo $true; ?></span></td>
																	<td colspan="2"><span><?php echo $false; ?></span></td>
																</tr>
															</tbody>
														</table>
													</div>
												</dt>
												<dd>.p-table + .is-middle + .is-scroll<br>thead.is-color__key1<br>rowspan colspan</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<div class="p-table is-middle">
														<table>
															<thead>
																<tr>
																	<th class="is-color__key1" rowspan="2"><span><?php echo $th; ?></span></th>
																	<th class="is-color__key1" colspan="2"><span><?php echo $th; ?></span></th>
																</tr>
																<tr>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																	<th class="is-color__key1"><span><?php echo $th; ?></span></th>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<td><span><?php echo $short; ?></span></td>
																	<td><span><?php echo $short; ?></span></td>
																	<td><span><?php echo $short; ?></span></td>
																</tr>
															</tbody>
														</table>
													</div>
												</dt>
												<dd>.p-table + .is-middle<br>thead.is-color__key1<br>rowspan colspan</dd>
											</dl>
										</li>



									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>

				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">FORM</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt>
													<?php
													$body =	setValueInput('radio', 'your-radio', 'カテゴリ', false, array('inputlist' => array(
														"doc" => "資料請求",
														"contact" => "お問い合わせ",
													),));
													$attrArray = [];

													echo renderInputField($body["type"], $body["name"], $body['inputlist'], $attrArray, '');
													?>

												</dt>
												<dd>.p-radio</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$body =	setValueInput('checkbox', 'your-radio', 'カテゴリ', false, array('inputlist' => array(
														"doc" => "資料請求",
														"contact" => "お問い合わせ",
													),));
													echo renderInputField($body["type"], $body["name"], $body['inputlist'], $attrArray, '');
													?>
												</dt>
												<dd>.p-checkbox</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$body = setValueInput('select', 'your-select', 'セレクトボックス', true, array('inputlist' => array(
														"doc" => "資料請求",
														"contact" => "お問い合わせ",
													),));
													echo renderInputField($body["type"], $body["name"], $body['inputlist'], $attrArray, '');
													?>
												</dt>
												<dd>.p-select</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$body = setValueInput('text', 'your-name', '氏名', true,	array('placeholder' => '例）山田 太郎', 'error' => '必須項目です',));
													echo renderInputField($body["type"], $body["name"], '', $attrArray, '');
													?>
												</dt>
												<dd>.p-input</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$body = setValueInput('textarea', 'your-message', 'お問合せ内容', false,	array('placeholder' => 'xxxxxxxxxxxxのご要望、その他お問い合せなど自由にご記入ください。'));
													echo renderInputField($body["type"], $body["name"], '', $attrArray, '');
													?>
												</dt>
												<dd>.p-textarea</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>

				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">SEARCH</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt><?php setHtmlSearch(); ?></dt>
												<dd>.p-search</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>

				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">PAGER</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt><?php setHtmlPagerNum(); ?></dt>
												<dd>.p-pager__num</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlPagerArr('/'); ?></dt>
												<dd>.p-pager__arr</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>

				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">SNS</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt><?php setHtmlSns(); ?></dt>
												<dd>.p-sns</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>

				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">SHARE</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt><?php setHtmlSnsshare(); ?></dt>
												<dd>.p-share</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>

				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">MAP</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt>
													<?php
													$body =  array(
														'mode' => 'iframe', //iframe,iframe_link,api
														'iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3255.1619227589626!2d139.43445795155552!3d35.32680035683735!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x60184dfa76e1ae43%3A0xa5c57aa86b013e27!2z44CSMjUzLTAwMzEg56We5aWI5bed55yM6IyF44O25bSO5biC5a-M5aOr6KaL55S677yR77yW4oiS77yT77yZ!5e0!3m2!1sja!2sjp!4v1660287810047!5m2!1sja!2sjp" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>'
													);
													setHtmlMap($body, "p-map");
													?>
												</dt>
												<dd>.p-map</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$body =  array(
														'mode' => 'iframe_link', //iframe,iframe_link,api
														'iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3255.1619227589626!2d139.43445795155552!3d35.32680035683735!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x60184dfa76e1ae43%3A0xa5c57aa86b013e27!2z44CSMjUzLTAwMzEg56We5aWI5bed55yM6IyF44O25bSO5biC5a-M5aOr6KaL55S677yR77yW4oiS77yT77yZ!5e0!3m2!1sja!2sjp!4v1660287810047!5m2!1sja!2sjp" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
														'link' => 'https://maps.app.goo.gl/ccQ3USGam6Zzjs4BA',
													);
													setHtmlMap($body, "p-map");
													?>
												</dt>
												<dd>.p-map + .is-ovh</dd>
											</dl>
										</li>

										<li>
											<dl>
												<dt>
													<?php
													$body =  array(
														'mode' => 'api', //iframe,iframe_link,api
														'lat' => "35.32697306801492",
														'lng' => "139.43685625823144",
														'pin' => $image_path . "common/pin.webp",
														'iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3255.1619227589626!2d139.43445795155552!3d35.32680035683735!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x60184dfa76e1ae43%3A0xa5c57aa86b013e27!2z44CSMjUzLTAwMzEg56We5aWI5bed55yM6IyF44O25bSO5biC5a-M5aOr6KaL55S677yR77yW4oiS77yT77yZ!5e0!3m2!1sja!2sjp!4v1660287810047!5m2!1sja!2sjp" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>'
													);
													setHtmlMap($body, "p-map");
													?>
												</dt>
												<dd>.p-map + .is-api</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>

				<!-- 	<section>
		<div class="section__wrap">
			<div class="<?php echo $TemplateClass; ?>__wrap">
				<div class="<?php echo $TemplateClass; ?>__head">
					<span class="<?php echo $TemplateClass; ?>__title">TEST</span>
				</div>
				<div class="<?php echo $TemplateClass; ?>__body">
					<div class="<?php echo $TemplateClass; ?>__content">
						<ul class="<?php echo $TemplateClass; ?>__list is-single">
							<li>
								<dl>
									<dt>
										<?php
										$code = '
<?php echo "Hello, world!"; ?>
	<?php echo "Hello, world!"; ?>
	<?php echo "Hello, world!"; ?>
<?php echo "Hello, world!"; ?>
										'; ?>
										<pre><?php echo htmlspecialchars($code); ?></pre>
									</dt>
									<dd></dd>
								</dl>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section> -->
				<!-- <section>
		<div class="section__wrap">
			<div class="<?php echo $TemplateClass; ?>__wrap">
				<div class="<?php echo $TemplateClass; ?>__head">
					<span class="<?php echo $TemplateClass; ?>__title">LINK</span>
				</div>
				<div class="<?php echo $TemplateClass; ?>__body">
					<div class="<?php echo $TemplateClass; ?>__content">
						<ul class="<?php echo $TemplateClass; ?>__list is-xxxx">
							<li>
								<dl>
									<dt></dt>
									<dd></dd>
								</dl>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section> -->








			</div>
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
		</main>

		<?php include($inc_path . "_l-foot.php"); ?>
	</div>
	<?php include($inc_path . "_l-script.php"); ?>
</body>

</html>
