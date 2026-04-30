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
			<?php
			$body = array(
				'title' =>  setValueTitle('News', "お知らせ"),
				'pankuzu' => array('home', "news"),
				'mv' => array(
					'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
				),
			);
			setHtmlMv($body, "b-mv__sub");
			?>
			<?php
			$body = array(
				'title' =>  setValueTitle('News', "お知らせ"),
				'pankuzu' => array('home', "news"),
				'mv' => array(
					'image' => setValueImage($image_path . '_dummy/pic-dummy_b.webp', '画像タイトル'),
					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
				),
			);
			setHtmlMv($body, "b-mv__sub2 is-white");
			?>
			<?php
			$body = array(
				'title' =>  setValueTitle('News', "お知らせ"),
				'pankuzu' => array('home', "news"),
				'mv' => array(
					'image' => '',
					'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れております。',
				),
			);
			setHtmlMv($body, "b-mv__noimage is-center");
			?>

			<div class="<?php echo $TemplateClass; ?>">
				<section style="border-top: 1px solid var(--Border);">
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap is-block">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">MV</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt>ページ先頭に表示</dt>
												<dd>.b-mv__sub</dd>
												<dd>.b-mv__sub2 + .is-white</dd>
												<dd>.b-mv__noimage + .is-center</dd>
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
						<div class="<?php echo $TemplateClass; ?>__wrap is-block">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">Box</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt>
													<?php
													$boxClass = 'b-box';
													$boxValue = array(
														'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', "画像タイトル"),
														'date' => date($date_format),
														'taxonomy' => array(
															'category' => array(
																(object) ['name' => 'カテゴリ1', 'slug' => 'category1', 'taxonomy' => 'category'],
															),
															'tag' => array(
																(object) ['name' => 'カテゴリ1', 'slug' => 'tag1', 'taxonomy' => 'tag'],
																(object) ['name' => 'カテゴリ2', 'slug' => 'tag2', 'taxonomy' => 'tag'],
																(object) ['name' => 'カテゴリ3', 'slug' => 'tag3', 'taxonomy' => 'tag'],
															),
														),
														'title' => setValueTitle('見出しが入ります', "Headline Title"),
														'lead' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
														'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
														'link' => array(
															'mode' => 'link',
															'link' => array(
																'title' => 'VIEW MORE',
																'url' => $link_path . "/",
																'target' => '',
															),
															'file' => false, //pdfなどときはこちら(必須じゃない)
														),
														'youtube'    => setValueYoutube('aqa9h-nL-TA', $image_path . '_dummy/pic-dummy.webp'),
														'video_mode' => 'modal', // 'modal' または 'autoplay'
													);
													['url' => $boxUrl, 'target' => $boxTarget] = SetBoxLink($boxValue['link']);
													?>
													<div class="<?php echo $boxClass; ?>">
														<div class="<?php echo $boxClass; ?>__wrap">
															<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
																<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
															</a>
															<div class="<?php echo $boxClass; ?>__textBox textBox">
																<div class="info">
																	<?php setHtmlText($boxValue['date'], 'p-date'); ?>
																	<?php setHtmlTaxonomy($boxValue['taxonomy']['category'], 'p-category', false, true); ?>
																</div>
																<a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
																	<?php setHtmlTitle($boxValue['title'], 'p-title__sec', 'h2'); ?>
																	<?php setHtmlText($boxValue['lead'], 'p-lead__s'); ?>
																	<?php setHtmlText($boxValue['text'], 'p-text'); ?>
																</a>
																<?php setHtmlTaxonomy($boxValue['taxonomy']['tag'], 'p-tag', false, true); ?>
																<?php setHtmlLink($boxValue['link'], 'p-button is-color__sub', 'icon-arrow'); ?>
															</div>
														</div>
													</div>
												</dt>
												<dd>
													.b-box
												</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$boxClass = 'b-box__tate';
													?>
													<div class="<?php echo $boxClass; ?>">
														<div class="<?php echo $boxClass; ?>__wrap">
															<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
																<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
															</a>
															<div class="<?php echo $boxClass; ?>__textBox textBox">
																<div class="info">
																	<?php setHtmlText($boxValue['date'], 'p-date'); ?>
																	<?php setHtmlTaxonomy($boxValue['taxonomy']['category'], 'p-category', false, true); ?>
																</div>
																<a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
																	<?php setHtmlTitle($boxValue['title'], 'p-title__sec', 'h2'); ?>
																	<?php setHtmlText($boxValue['lead'], 'p-lead__s'); ?>
																	<?php setHtmlText($boxValue['text'], 'p-text'); ?>
																</a>
																<?php setHtmlTaxonomy($boxValue['taxonomy']['tag'], 'p-tag', false, true); ?>
																<?php setHtmlLink($boxValue['link'], 'p-button is-color__sub', 'icon-arrow'); ?>
															</div>
														</div>
													</div>
												</dt>
												<dd>
													.b-box__tate
												</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$boxClass = 'b-box__card';
													?>
													<div class="<?php echo $boxClass; ?>">
														<div class="<?php echo $boxClass; ?>__wrap">
															<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
																<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
															</a>
															<div class="<?php echo $boxClass; ?>__textBox textBox">
																<div class="info">
																	<?php setHtmlText($boxValue['date'], 'p-date'); ?>
																	<?php setHtmlTaxonomy($boxValue['taxonomy']['category'], 'p-category', false, true); ?>
																</div>
																<a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
																	<?php setHtmlTitle($boxValue['title'], 'p-title__box', 'h3'); ?>
																	<?php setHtmlText($boxValue['text'], 'p-text'); ?>
																</a>
																<?php setHtmlTaxonomy($boxValue['taxonomy']['tag'], 'p-tag', false, true); ?>
															</div>
														</div>
													</div>
												</dt>
												<dd>
													.b-box__card
												</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$boxClass = 'b-box__yoko';
													?>
													<div class="<?php echo $boxClass; ?>">
														<div class="<?php echo $boxClass; ?>__wrap">
															<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
																<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
															</a>
															<div class="<?php echo $boxClass; ?>__textBox textBox">
																<div class="info">
																	<?php setHtmlText($boxValue['date'], 'p-date'); ?>
																	<?php setHtmlTaxonomy($boxValue['taxonomy']['category'], 'p-category', false, true); ?>
																</div>
																<a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
																	<?php setHtmlTitle($boxValue['title'], 'p-title__sec', 'h2'); ?>
																	<?php setHtmlText($boxValue['lead'], 'p-lead__s'); ?>
																	<?php setHtmlText($boxValue['text'], 'p-text'); ?>
																</a>
																<?php setHtmlTaxonomy($boxValue['taxonomy']['tag'], 'p-tag', false, true); ?>
																<?php setHtmlLink($boxValue['link'], 'p-button is-color__sub', 'icon-arrow'); ?>
															</div>
														</div>
													</div>
												</dt>
												<dd>
													.b-box__yoko
												</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$boxClass = 'b-box__yokoFull';
													?>
													<div class="<?php echo $boxClass; ?>">
														<div class="<?php echo $boxClass; ?>__wrap">
															<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
																<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
															</a>
															<div class="<?php echo $boxClass; ?>__textBox textBox">
																<a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
																	<?php setHtmlTitle($boxValue['title'], 'p-title__sec', 'h2'); ?>
																	<?php setHtmlText($boxValue['lead'], 'p-lead__s'); ?>
																	<?php setHtmlText($boxValue['text'], 'p-text'); ?>
																</a>
																<?php setHtmlLink($boxValue['link'], 'p-button is-color__sub', 'icon-arrow'); ?>
															</div>
														</div>
													</div>
												</dt>
												<dd>
													.b-box__yokoFull
												</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$boxClass = 'b-box__bnr';
													?>
													<div class="<?php echo $boxClass; ?>">
														<div class="<?php echo $boxClass; ?>__wrap">
															<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
																<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
															</a>
															<div class="<?php echo $boxClass; ?>__textBox textBox">
																<a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
																	<?php setHtmlTitle($boxValue['title'], 'p-title__sec', 'h2'); ?>
																	<?php setHtmlText($boxValue['lead'], 'p-lead__s'); ?>
																	<?php setHtmlText($boxValue['text'], 'p-text'); ?>
																</a>
																<?php setHtmlLink($boxValue['link'], 'p-button is-color__sub', 'icon-arrow'); ?>
															</div>
														</div>
													</div>
												</dt>
												<dd>
													.b-box__bnr
												</dd>
											</dl>
										</li>

										<li>
											<dl>
												<dt>
													<?php
													$boxClass = 'b-box__profile';
													?>
													<div class="<?php echo $boxClass; ?>">
														<div class="<?php echo $boxClass; ?>__wrap">
															<div class="<?php echo $boxClass; ?>__imageBox imageBox">
																<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
															</div>
															<div class="<?php echo $boxClass; ?>__textBox textBox">
																<div class="titleBox">
																	<?php setHtmlTitle($boxValue['title'], 'p-title__box', 'h2'); ?>
																	<?php setHtmlText($boxValue['text'], 'p-text'); ?>
																</div>
																<?php setHtmlLink($boxValue['link'], 'p-link is-color__sub', 'icon-arrow'); ?>
															</div>
														</div>
													</div>
												</dt>
												<dd>
													.b-box__profile
												</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$boxClass = 'b-box__profileSquare';
													?>
													<div class="<?php echo $boxClass; ?>">
														<div class="<?php echo $boxClass; ?>__wrap">
															<div class="<?php echo $boxClass; ?>__imageBox imageBox">
																<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
															</div>
															<div class="<?php echo $boxClass; ?>__textBox textBox">
																<div class="titleBox">
																	<?php setHtmlTitle($boxValue['title'], 'p-title__box', 'h2'); ?>
																	<?php setHtmlText($boxValue['text'], 'p-text'); ?>
																</div>
																<?php setHtmlLink($boxValue['link'], 'p-link is-color__sub', 'icon-arrow'); ?>
															</div>
														</div>
													</div>
												</dt>
												<dd>
													.b-box__profileSquare
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
						<div class="<?php echo $TemplateClass; ?>__wrap is-block">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">LIST</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt>
													<?php
													$blockClass = 'b-list__col4';
													$blockValue = array(
														$boxValue,
														$boxValue,
														$boxValue,
														$boxValue,
													);
													?>
													<div class="<?php echo $blockClass; ?>">
														<div class="<?php echo $blockClass; ?>__wrap">
															<ul class="<?php echo $blockClass; ?>__ul">
																<?php foreach ($blockValue as $list) : ?>
																	<li class="<?php echo $blockClass; ?>__li">

																		<?php
																		$boxClass = 'b-box__tate';
																		$boxValue = $list;
																		['url' => $boxUrl, 'target' => $boxTarget] = SetBoxLink($boxValue['link']);
																		?>
																		<div class="<?php echo $boxClass; ?>">
																			<div class="<?php echo $boxClass; ?>__wrap">
																				<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
																					<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
																				</a>
																				<div class="<?php echo $boxClass; ?>__textBox textBox">
																					<div class="info">
																						<?php setHtmlText($boxValue['date'], 'p-date'); ?>
																						<?php setHtmlTaxonomy($boxValue['taxonomy']['category'], 'p-category', false, true); ?>
																					</div>
																					<a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
																						<?php setHtmlTitle($boxValue['title'], 'p-title__box', 'h2'); ?>
																						<?php setHtmlText($boxValue['text'], 'p-text'); ?>
																					</a>
																					<?php setHtmlTaxonomy($boxValue['taxonomy']['tag'], 'p-tag', false, true); ?>
																					<?php setHtmlLink($boxValue['link'], 'p-button is-color__sub', 'icon-arrow'); ?>
																				</div>
																			</div>
																		</div>

																	</li>
																<?php endforeach; ?>
															</ul>
														</div>
													</div>
												</dt>
												<dd>
													.b-list__col4 | ブログの一覧など
												</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$blockClass = 'b-list__col3';
													$blockValue = array(
														$boxValue,
														$boxValue,
														$boxValue,
													);
													?>
													<div class="<?php echo $blockClass; ?>">
														<div class="<?php echo $blockClass; ?>__wrap">
															<ul class="<?php echo $blockClass; ?>__ul">
																<?php foreach ($blockValue as $list) : ?>
																	<li class="<?php echo $blockClass; ?>__li">
																		<?php
																		$boxClass = 'b-box__tate';
																		$boxValue = $list;
																		['url' => $boxUrl, 'target' => $boxTarget] = SetBoxLink($boxValue['link']);
																		?>
																		<div class="<?php echo $boxClass; ?>">
																			<div class="<?php echo $boxClass; ?>__wrap">
																				<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
																					<?php setHtmlBgImage($boxValue['image'], 'p-image is-link'); ?>
																				</a>
																			</div>
																		</div>
																	</li>
																<?php endforeach; ?>
															</ul>
														</div>
													</div>
												</dt>
												<dd>
													.b-list__col3 | 画像リンク
												</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$blockClass = 'b-list__col3';
													// postの時は下記のようにblockValueを設定する
													$articleValue = array(
														'cpt' => "news",
														'list' =>  array(0, 0, 0, 0, 0, 0),
														'pager' => false
													);
													$blockValue = getPostListArray($articleValue);
													?>
													<div class="<?php echo $blockClass; ?>">
														<div class="<?php echo $blockClass; ?>__wrap">
															<ul class="<?php echo $blockClass; ?>__ul">
																<?php foreach ($blockValue as $list) : ?>
																	<li class="<?php echo $blockClass; ?>__li">
																		<?php
																		$boxClass = 'b-box__card';
																		$boxValue = $list;
																		['url' => $boxUrl, 'target' => $boxTarget] = SetBoxLink($boxValue['link']);
																		?>
																		<div class="<?php echo $boxClass; ?>">
																			<div class="<?php echo $boxClass; ?>__wrap">
																				<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
																					<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
																				</a>
																				<div class="<?php echo $boxClass; ?>__textBox textBox">
																					<div class="info">
																						<?php setHtmlText($boxValue['date'], 'p-date'); ?>
																						<?php setHtmlTaxonomy($boxValue['taxonomy']['category'], 'p-category', false, true); ?>
																					</div>
																					<a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
																						<?php setHtmlTitle($boxValue['title'], 'p-title__box', 'h2'); ?>
																						<?php setHtmlText($boxValue['text'], 'p-text'); ?>
																					</a>
																					<?php setHtmlTaxonomy($boxValue['taxonomy']['tag'], 'p-tag', false, true); ?>
																				</div>
																			</div>
																		</div>

																	</li>
																<?php endforeach; ?>
															</ul>
														</div>
													</div>
												</dt>
												<dd>
													.b-list__col4 | ブログの一覧など
												</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$blockClass = 'b-list__flex';
													$blockValue = array(
														$boxValue,
														$boxValue,
														$boxValue,
														$boxValue,
													);
													?>
													<div class="<?php echo $blockClass; ?>">
														<div class="<?php echo $blockClass; ?>__wrap">
															<ul class="<?php echo $blockClass; ?>__ul">
																<?php foreach ($blockValue as $list) : ?>
																	<li class="<?php echo $blockClass; ?>__li">
																		<?php
																		$boxClass = 'b-box__yoko';
																		$boxValue = $list;
																		['url' => $boxUrl, 'target' => $boxTarget] = SetBoxLink($boxValue['link']);
																		?>
																		<div class="<?php echo $boxClass; ?>">
																			<div class="<?php echo $boxClass; ?>__wrap">
																				<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
																					<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
																				</a>
																				<div class="<?php echo $boxClass; ?>__textBox textBox">
																					<a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
																						<?php setHtmlTitle($boxValue['title'], 'p-title__box', 'h2'); ?>
																						<?php setHtmlText($boxValue['text'], 'p-text'); ?>
																					</a>
																					<?php setHtmlLink($boxValue['link'], 'p-button is-color__sub', 'icon-arrow'); ?>
																				</div>
																			</div>
																		</div>
																	</li>
																<?php endforeach; ?>
															</ul>
														</div>
													</div>
												</dt>
												<dd>
													.b-list__flex | 中の要素で.b-box__yokoを指定
												</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$blockClass = 'b-list__col4';
													$blockValue = array(
														$boxValue,
														$boxValue,
														$boxValue,
														$boxValue,
														$boxValue,
														$boxValue,
														$boxValue,
														$boxValue,
													);
													?>
													<div class="<?php echo $blockClass; ?>">
														<div class="<?php echo $blockClass; ?>__wrap">
															<ul class="<?php echo $blockClass; ?>__ul">
																<?php foreach ($blockValue as $list) : ?>
																	<li class="<?php echo $blockClass; ?>__li">
																		<?php
																		$boxClass = 'b-box__yoko';
																		$boxValue = $list;
																		['url' => $boxUrl, 'target' => $boxTarget] = SetBoxLink($boxValue['link']);
																		?>
																		<div class="<?php echo $boxClass; ?>">
																			<div class="<?php echo $boxClass; ?>__wrap">
																				<div href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
																					<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
																				</div>

																			</div>
																		</div>
																	</li>
																<?php endforeach; ?>
															</ul>
														</div>
													</div>
												</dt>
												<dd>
													.b-list__col4 | gallery
												</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php
													$blockClass = 'b-list__news';
													$blockValue = array(
														$boxValue,
														$boxValue,
														$boxValue,
														$boxValue,
													);
													?>
													<div class="<?php echo $blockClass; ?>">
														<div class="<?php echo $blockClass; ?>__wrap">
															<ul class="<?php echo $blockClass; ?>__ul">
																<?php foreach ($blockValue as $list) : ?>
																	<li class="<?php echo $blockClass; ?>__li">
																		<?php
																		$boxClass = 'b-news';
																		$boxValue = $list;
																		['url' => $boxUrl, 'target' => $boxTarget] = SetBoxLink($boxValue['link']);
																		?>
																		<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>">
																			<dl class="<?php echo $boxClass; ?>__dl">
																				<dt class="<?php echo $boxClass; ?>__dt">
																					<?php setHtmlText($boxValue['date'], 'p-date'); ?>
																					<?php setHtmlTaxonomy($boxValue['taxonomy']['category'], 'p-category', false, false); ?>
																				</dt>
																				<dd class="<?php echo $boxClass; ?>__dd">
																					<?php setHtmlTitle($boxValue['title'], 'p-title__box', 'h2'); ?>
																				</dd>
																			</dl>
																		</a>
																	</li>
																<?php endforeach; ?>
															</ul>
														</div>
													</div>
												</dt>
												<dd>
													.b-list__news
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
						<div class="<?php echo $TemplateClass; ?>__wrap is-block">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">SPLIDE</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt>
													<?php

													$blockClass = 'b-splide';
													$splideSetting = array(
														"type" => "loop",
														"drag" => "free",
														"perPage" => 4,
														"gap" => "2rem",
														"pagination" => true,
														"arrows" => true,
														"focus" => 1,
														"padding" => array("left" => 0, "right" => "0%"),
														"autoplay" => true,
														"focus" => 0,
														"interval" => 4000,
														"rewind" => true,
														"snap" => true,
														"breakpoints" => array(
															"1000" => array("perPage" => 3),
															"680" => array("perPage" => 2)
														)
													);
													$blockValue = array(
														$boxValue,
														$boxValue,
														$boxValue,
														$boxValue,
														$boxValue,
														$boxValue,
													);
													?>
													<div class="<?php echo $blockClass; ?> js-splide splide" role="group" data-splide='<?php echo json_encode($splideSetting); ?>'>
														<div class="<?php echo $blockClass; ?>__wrap splide__track">
															<ul class="<?php echo $blockClass; ?>__ul splide__list">
																<?php foreach ($blockValue as $list) : ?>
																	<li class="<?php echo $blockClass; ?>__li splide__slide">
																		<?php
																		$boxClass = 'b-box__tate';
																		$boxValue = $list;
																		['url' => $boxUrl, 'target' => $boxTarget] = SetBoxLink($boxValue['link']);
																		?>
																		<div class="<?php echo $boxClass; ?>">
																			<div class="<?php echo $boxClass; ?>__wrap">
																				<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
																					<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
																				</a>
																				<div class="<?php echo $boxClass; ?>__textBox textBox">
																					<div class="info">
																						<?php setHtmlText($boxValue['date'], 'p-date'); ?>
																						<?php setHtmlTaxonomy($boxValue['taxonomy']['category'], 'p-category', false, true); ?>
																					</div>
																					<a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
																						<?php setHtmlTitle($boxValue['title'], 'p-title__box', 'h2'); ?>
																						<?php setHtmlText($boxValue['text'], 'p-text'); ?>
																					</a>
																					<?php setHtmlTaxonomy($boxValue['taxonomy']['tag'], 'p-tag', false, true); ?>
																					<?php setHtmlLink($boxValue['link'], 'p-button is-color__sub', 'icon-arrow'); ?>
																				</div>
																			</div>
																		</div>
																	</li>
																<?php endforeach; ?>
															</ul>
														</div>
														<div class="splide__ctrl">
															<div class="splide__arrows">
																<button class="splide__arrow splide__arrow--prev"></button>
																<ul class="splide__pagination"></ul>
																<button class="splide__arrow splide__arrow--next"></button>
															</div>
														</div>
													</div>
												</dt>
												<dd>
													.b-splide | article
												</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php

													$blockClass = 'b-splide';
													$splideSetting = array(
														"type" => "loop",
														"drag" => "free",
														"perPage" => 4,
														"gap" => "2rem",
														"pagination" => true,
														"arrows" => true,
														"focus" => 1,
														"padding" => array("left" => 0, "right" => "0%"),
														"autoplay" => true,
														"focus" => 0,
														"interval" => 4000,
														"rewind" => true,
														"snap" => true,
														"breakpoints" => array(
															"1000" => array("perPage" => 3),
															"680" => array("perPage" => 2)
														)
													);
													$blockValue = array(
														$boxValue,
														$boxValue,
														$boxValue,
														$boxValue,
														$boxValue,
														$boxValue,
													);
													?>
													<div class="<?php echo $blockClass; ?> js-splide splide" role="group" data-splide='<?php echo json_encode($splideSetting); ?>'>
														<div class="<?php echo $blockClass; ?>__wrap splide__track">
															<ul class="<?php echo $blockClass; ?>__ul splide__list">
																<?php foreach ($blockValue as $list) : ?>
																	<li class="<?php echo $blockClass; ?>__li splide__slide">
																		<?php
																		$boxClass = 'b-box__tate';
																		$boxValue = $list;
																		['url' => $boxUrl, 'target' => $boxTarget] = SetBoxLink($boxValue['link']);
																		?>
																		<div class="<?php echo $boxClass; ?>">
																			<div class="<?php echo $boxClass; ?>__wrap">
																				<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
																					<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
																				</a>
																				<div class="<?php echo $boxClass; ?>__textBox textBox">
																					<a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
																						<?php setHtmlTitle($boxValue['title'], 'p-title__box', 'h2'); ?>
																						<?php setHtmlText($boxValue['text'], 'p-text'); ?>
																					</a>
																					<?php setHtmlLink($boxValue['link'], 'p-button is-color__sub', 'icon-arrow'); ?>
																				</div>
																			</div>
																		</div>
																	</li>
																<?php endforeach; ?>
															</ul>
														</div>
														<div class="splide__ctrl">
															<div class="splide__arrows">
																<button class="splide__arrow splide__arrow--prev"></button>
																<ul class="splide__pagination"></ul>
																<button class="splide__arrow splide__arrow--next"></button>
															</div>
														</div>
													</div>
												</dt>
												<dd>
													.b-splide | box
												</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<?php

													$blockClass = 'b-splide';
													$splideSetting = array(
														"type" => "loop",
														"drag" => "free",
														"perPage" => 4,
														"gap" => "2rem",
														"pagination" => true,
														"arrows" => true,
														"focus" => 1,
														"padding" => array("left" => 0, "right" => "0%"),
														"autoplay" => true,
														"focus" => 0,
														"interval" => 4000,
														"rewind" => true,
														"snap" => true,
														"breakpoints" => array(
															"1000" => array("perPage" => 3),
															"680" => array("perPage" => 2)
														)
													);
													$blockValue = array(
														$boxValue,
														$boxValue,
														$boxValue,
														$boxValue,
														$boxValue,
														$boxValue,
													);
													?>
													<div class="<?php echo $blockClass; ?> js-splide splide" role="group" data-splide='<?php echo json_encode($splideSetting); ?>'>
														<div class="<?php echo $blockClass; ?>__wrap splide__track">
															<ul class="<?php echo $blockClass; ?>__ul splide__list">
																<?php foreach ($blockValue as $list) : ?>
																	<li class="<?php echo $blockClass; ?>__li splide__slide">
																		<?php
																		$boxClass = 'b-box__tate';
																		$boxValue = $list;
																		['url' => $boxUrl, 'target' => $boxTarget] = SetBoxLink($boxValue['link']);
																		?>
																		<div class="<?php echo $boxClass; ?>">
																			<div class="<?php echo $boxClass; ?>__wrap">
																				<?php
																				$mode = $boxValue['video_mode'] ?? '';
																				if ($mode === 'modal') :
																					$yt    = $boxValue['youtube'];
																					$cover = $yt['image']['sizes']['medium'] ?? '';
																					$ytId  = $yt['id'] ?? '';
																				?>
																					<button class="js-modal__open <?php echo $boxClass; ?>__imageBox imageBox"
																						data-modal-type="youtube"
																						data-youtube-id="<?php echo $ytId; ?>"
																						style="background-image:url(<?php echo $cover; ?>)">
																					</button>
																				<?php elseif ($mode === 'autoplay') :
																					setHtmlYoutube(
																						$boxValue['youtube'],
																						$boxClass . '__youtube',
																						'data-controls="false" data-mute="true" data-loop="true" data-auto="true" data-ratio="56.25"'
																					);
																				else : ?>
																					<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>"
																						class="<?php echo $boxClass; ?>__imageBox imageBox">
																						<?php setHtmlBgImage($boxValue['image'], 'p-image is-play'); ?>
																					</a>
																				<?php endif;
																				?>
																			</div>
																		</div>
																	</li>
																<?php endforeach; ?>
															</ul>
														</div>
														<div class="splide__ctrl">
															<div class="splide__arrows">
																<button class="splide__arrow splide__arrow--prev"></button>
																<ul class="splide__pagination"></ul>
																<button class="splide__arrow splide__arrow--next"></button>
															</div>
														</div>
													</div>
												</dt>
												<dd>
													.b-splide | gallery
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
						<div class="<?php echo $TemplateClass; ?>__wrap is-block">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">FAQ</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<?php
								$body = array(
									array(
										'dt' => $dummy_text,
										'dd' => $dummy_text . $dummy_text . $dummy_text,
									),
									array(
										'dt' => $dummy_text,
										'dd' => $dummy_text . $dummy_text . $dummy_text,
									),
									array(
										'dt' => $dummy_text,
										'dd' => $dummy_text . $dummy_text . $dummy_text,
									)
								);
								?>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt><?php setHtmlFaq($body, "b-faq"); ?></dt>
												<dd>.b-faq</dd>
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
						<div class="<?php echo $TemplateClass; ?>__wrap is-block">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">GALLERY</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<?php
								$body = array(
									'gallery' => array(
										setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
										setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
										setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
										setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
										setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
										setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
										setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
										setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
									)
								);
								$splideSetting = array(
									"type" => "loop",
									"drag" => "free",
									"perPage" => 4,
									"gap" => "2rem",
									"pagination" => false,
									"arrows" => false,
									"focus" => 1,
									"padding" => array("left" => 0, "right" => "0%"),
									"autoplay" => true,
									"focus" => 0,
									"interval" => 4000,
									"rewind" => true,
									"snap" => true,
									"autoScroll" => array(
										"speed" => 1,
										"rewind" => false,
										"pauseOnHover" => false,
										"pauseOnFocus" => false,
									),
									"breakpoints" => array(
										"1000" => array("perPage" => 3),
										"680" => array("perPage" => 2)
									)
								);
								?>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dd>.b-gallery</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt><?php setHtmlSplide($body, "b-splide", $splideSetting); ?></dt>
												<dd>.b-splide</dd>
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
						<div class="<?php echo $TemplateClass; ?>__wrap is-block">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">HISTORY</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<?php
								$body = array(
									array(
										'year' => '2023',
										'list' => array(
											array(
												'month' => '7',
												'title' => 'この文章はダミーです。',
												'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
											),
											array(
												'month' => '7',
												'title' => 'この文章はダミーです。',
												'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
											),
										),
									),
									array(
										'year' => '2022',
										'list' => array(
											array(
												'month' => '7',
												'title' => 'この文章はダミーです。',
												'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
											),
											array(
												'month' => '7',
												'title' => 'この文章はダミーです。',
												'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
											),
										),
									),
								);
								?>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt><?php setHtmlHistory($body, "b-history"); ?></dt>
												<dd>.b-history</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>
				<!-- <section>
		<div class="section__wrap">
			<div class="<?php echo $TemplateClass; ?>__wrap is-block">
				<div class="<?php echo $TemplateClass; ?>__head">
					<span class="<?php echo $TemplateClass; ?>__title">PROFILE</span>
				</div>
				<div class="<?php echo $TemplateClass; ?>__body">
					<?php
					$body = array(
						'imageBox' => array(
							'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
						),
						'textBox' => array(
							'title' => setValueTitle("山田太郎", "代表取締役"),
							'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text,
							'links' => array(
								array(
									'mode' => 'link',
									'link' => array(
										'title' => 'VIEW MORE',
										'url' => $link_path,
										'target' => '',
									),
									'file' => false,
								),
								array(
									'mode' => 'link',
									'link' => array(
										'title' => 'VIEW MORE',
										'url' => $link_path,
										'target' => '',
									),
									'file' => false,
								),
							)
						),
					);
					?>
					<div class="<?php echo $TemplateClass; ?>__content">
						<ul class="<?php echo $TemplateClass; ?>__list is-single">
							<li>
								<dl>
									<dt><?php setHtmlProfile($body, "b-profile"); ?></dt>
									<dd>.b-faq</dd>
								</dl>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section> -->
				<section>
					<div class="section__wrap">
						<div class="<?php echo $TemplateClass; ?>__wrap is-block">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">ppcontact</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<?php
								$body = array(
									'title' => "お問い合わせ先",
									'text' => $client_name . ' 広報担当<br><a class="p-link" href="mailto:' . $email . '"><span>' . $email . '</span></a>'
								);
								?>
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt><?php setHtmlPpcontact($body, "b-ppcontact"); ?></dt>
												<dd>.b-ppcontact</dd>
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
						<div class="<?php echo $TemplateClass; ?>__wrap is-block">
							<div class="<?php echo $TemplateClass; ?>__head">
								<span class="<?php echo $TemplateClass; ?>__title">Links</span>
							</div>
							<div class="<?php echo $TemplateClass; ?>__body">
								<div class="<?php echo $TemplateClass; ?>__content">
									<ul class="<?php echo $TemplateClass; ?>__list is-single">
										<li>
											<dl>
												<dt>
													<div class="b-links">
														<a href="#" class="p-button is-reverse"><?php setHtmlSvg("icon-arrow3"); ?><span>VIEW MORE</span></a>
													</div>
												</dt>
												<dd>.b-links</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<div class="b-links">
														<a href="#" class="p-button is-reverse"><?php setHtmlSvg("icon-arrow3"); ?><span>VIEW MORE</span></a>
														<a href="#" class="p-button is-color__sub is-reverse"><?php setHtmlSvg("icon-arrow3"); ?><span>VIEW MORE</span></a>
													</div>
												</dt>
												<dd>.b-links</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<div class="b-links">
														<a href="#" class="p-link is-reverse"><?php setHtmlSvg("icon-arrow3"); ?><span>VIEW MORE</span></a>
													</div>
												</dt>
												<dd>.b-links</dd>
											</dl>
										</li>
										<li>
											<dl>
												<dt>
													<div class="b-links">
														<a href="#" class="p-link is-reverse"><?php setHtmlSvg("icon-arrow3"); ?><span>VIEW MORE</span></a>
														<a href="#" class="p-link is-reverse"><?php setHtmlSvg("icon-arrow3"); ?><span>VIEW MORE</span></a>
													</div>
												</dt>
												<dd>.b-links</dd>
											</dl>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>

				<?php
				// まだ追加できていないブロック
				// $body = array(
				// 	array(
				// 		'r' => true,
				// 		'imageBox' => array(
				// 			'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
				// 			'name' => "山田太郎",
				// 		),
				// 		'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text,
				// 	),
				// 	array(
				// 		'r' => false,
				// 		'imageBox' => array(
				// 			'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', 'テスト画像'),
				// 			'name' => "山田太郎",
				// 		),
				// 		'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text,
				// 	),
				// );
				// setHtmlInterview($body, "b-interview");


				// $body = array(
				// 	array(
				// 		'time' => '10:00 ~',
				// 		'title' => setValueTitle("公演のタイトル", "公演のサブタイトルを書く"),
				// 		'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text,
				// 		'sub' => "山田太郎",
				// 	),
				// 	array(
				// 		'time' => '10:00 ~',
				// 		'title' => setValueTitle("公演のタイトル", "公演のサブタイトルを書く"),
				// 		'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text,
				// 		'sub' => "山田太郎",
				// 	),
				// 	array(
				// 		'time' => '10:00 ~',
				// 		'title' => setValueTitle("公演のタイトル", "公演のサブタイトルを書く"),
				// 		'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text,
				// 		'sub' => "山田太郎",
				// 	),
				// );
				// setHtmlTimeTable($body, "b-timeTable");


				// $body = array(
				// 	array(
				// 		'time' => '10:00 ~',
				// 		'title' => setValueTitle("公演のタイトル", "公演のサブタイトルを書く"),
				// 		'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text,
				// 		'sub' => "山田太郎",
				// 	),
				// 	array(
				// 		'time' => '10:00 ~',
				// 		'title' => setValueTitle("公演のタイトル", "公演のサブタイトルを書く"),
				// 		'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text,
				// 		'sub' => "山田太郎",
				// 	),
				// 	array(
				// 		'time' => '10:00 ~',
				// 		'title' => setValueTitle("公演のタイトル", "公演のサブタイトルを書く"),
				// 		'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text,
				// 		'sub' => "山田太郎",
				// 	),
				// );
				// setHtmlTimeTable($body, "b-timeTable");


				// $body = array(
				// 	array(
				// 		"mode" => "file",
				// 		'date' => date('Y.m.d'),
				// 		'icon' => 'icon-file',
				// 		'title' => $dummy_text,
				// 		'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text,
				// 		'file' => $link_path . '',
				// 	),
				// 	array(
				// 		"mode" => "link",
				// 		'date' => date('Y.m.d'),
				// 		'title' => $dummy_text,
				// 		'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text,
				// 		'link' => array(
				// 			'url' => $link_path . "/",
				// 			'target' =>  "_self",
				// 		),
				// 	),
				// 	array(
				// 		"mode" => "link",
				// 		'date' => date('Y.m.d'),
				// 		'title' => $dummy_text,
				// 		'text' => $dummy_text . $dummy_text . $dummy_text . $dummy_text,
				// 		'link' => array(
				// 			'url' =>  "https://google.com",
				// 			'target' =>  "_blank",
				// 		),
				// 	),
				// );
				// setHtmlLinkTable($body, "b-linkTable");


				// $body = array(
				// 	array(
				// 		"box" => array(
				// 			array(
				// 				"acf_fc_layout" => "title",
				// 				"title" => array(
				// 					"h" => "h2",
				// 					"main" => "セクション1",
				// 					"sub" => "セクションのサブタイトル"
				// 				)
				// 			),
				// 			array(
				// 				"acf_fc_layout" => "title",
				// 				"title" => array(
				// 					"h" => "h2",
				// 					"main" => "セクション2",
				// 					"sub" => "セクションのサブタイトル"
				// 				)
				// 			),
				// 		),
				// 	),
				// 	array(
				// 		"box" => array(
				// 			array(
				// 				"acf_fc_layout" => "title",
				// 				"title" => array(
				// 					"h" => "h2",
				// 					"main" => "セクション3",
				// 					"sub" => "セクションのサブタイトル"
				// 				)
				// 			),
				// 			array(
				// 				"acf_fc_layout" => "title",
				// 				"title" => array(
				// 					"h" => "h2",
				// 					"main" => "セクション4",
				// 					"sub" => "セクションのサブタイトル"
				// 				)
				// 			),
				// 		),
				// 	),
				// );
				// setHtmlAgenda($body, "b-agenda");



				// $body = array(
				// 	'title' =>  setValueTitle('ブログの見出しが25文字程度で入ります。長めで2行くらいになりそうです。'),
				// 	'date' => date($date_format),
				// 	'taxonomy' => array(
				// 		'category' => array($news_value['category'][1]),
				// 	),
				// );
				// setHtmlWysiwyg($body, "b-wysiwyg", "news");
				?>
			</div>
		</main>

		<?php include($inc_path . "_l-foot.php"); ?>
	</div>
	<?php include($inc_path . "_l-script.php"); ?>

</body>

</html>
