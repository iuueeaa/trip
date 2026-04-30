<?php
$url = str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__FILE__));
$url = ltrim($url, '/');
if (strpos($url, '/') == "") :
	$url = $url;
else :
	$num = strpos($url, '/');
	$url = substr($url, 0, $num);
endif;
$local_path       = is_numeric($url) ? '/' . $url : '';
$root_path        = $_SERVER['DOCUMENT_ROOT'] . $local_path;

$link_path = $local_path;
$lang = (isset($_GET['lang'])) ? $_GET['lang'] : 'ja';
$wpflg = false;
include($root_path . "/assets/inc/value/_common.php");
include($root_path . "/assets/inc/value/" . $lang . ".php");
?>
<html>

<head>
	<title>サイトマップ資料</title>
	<style>
		html {
			font-size: 6pt;
		}

		body {
			background-color: eee;
			--pageGap: 36pt;
			--pageTop: calc(3rem + 0px);
			--pageWidth: 12rem;
		}

		.a4 {
			width: 210mm;
			height: 294mm;
			background: #fff;
			padding: 10mm;
			position: relative;

			* {
				font-size: 7pt;
				margin: 0;
			}
		}

		.a4__wrap {
			/* display: grid;
			grid-template-columns: repeat(4, 1fr);
			gap: 12pt; */
		}

		.a4__foot {
			position: absolute;
			width: 100%;
			text-align: center;
			left: 0;
			bottom: 7mm;
			font-size: 4pt;
			color: var(--SubText);
		}

		.sitemap {
			display: grid;
			grid-template-columns: var(--pageWidth) 1fr;
			gap: var(--pageGap);
		}

		.sitemap__ul {
			padding: 0;
			position: relative;
		}

		.sitemap__ul::before {
			content: "";
			position: absolute;
			border-left: 1px solid #eee;
			left: calc(var(--pageGap) / 2 * -1);
			top: calc(var(--pageTop) / 2 + 1px);
			height: calc(100% - var(--pageTop) - 1px);
			width: 1px;
		}

		.sitemap__ul::after {
			content: "";
			display: block;
			width: calc(var(--pageGap) / 2);
			height: 1px;
			border-top: 1px solid #eee;
			position: absolute;
			top: calc(var(--pageTop) / 2 + 1px);
			left: calc(var(--pageGap) / 2 * -1);
			transform: translate(-100%, 0);
		}

		.sitemap__li {
			display: grid;
			grid-template-columns: var(--pageWidth) 1fr;
			gap: var(--pageGap);
		}

		.sitemap__li+li {
			margin-top: 1rem;
		}

		.page {}

		.page__wrap {
			border: 1px solid #eee;
			padding: 1rem;
			display: block;
			position: relative;
		}

		.page__wrap::before {
			content: "";
			display: block;
			width: calc(var(--pageGap) / 2);
			height: 1px;
			border-top: 1px solid #eee;
			position: absolute;
			left: 0;
			top: 50%;
			transform: translate(-100%, 0);
		}

		.page__wrap.wpok {
			background-color: #bdf8ff;
		}

		.page__wrap p {
			font-size: 1rem;
			line-height: 1;
			white-space: nowrap;
			text-align: center;
		}

		dl {
			display: flex;
			gap: 1em;
		}
	</style>
</head>

<body>

	<div class="a4">
		<div class="a4__wrap">
			<div class="a4__title">サイトマップ</div>
			<div class="sitemap">
				<div class="page"><span class="page__wrap">TOP</span></div>
				<?php SitemapHtml($sitemapArr); ?>
			</div>
		</div>
		<div class=" a4__foot">
			<p><span>delauany.jp</span></p>
		</div>
	</div>
</body>

</html>


<?php
function SitemapHtml($array)
{
?>
	<ul class="sitemap__ul">
		<?php foreach ($array as $key => $value) : ?>
			<?php if ($value["post_type"] != "top") : ?>
				<?php $pclass = ($value["check"]["wp"]) ? "wpok" : ""; ?>
				<li class="sitemap__li ">
					<div class="page" :data-rank="<?php echo $value["rank"]; ?>">
						<div class="page__wrap <?php echo $pclass ?>">
							<p><?php echo $value["name"]; ?></p>
						</div>
					</div>
					<?php
					if (!empty($value["children"]) && count($value["children"]) > 1) {
						SitemapHtml($value["children"]);
					}
					?>
					<?php if (!empty($value["post_type"] == "post")) : ?>
						<ul class="sitemap__ul">
							<li class="sitemap__li">
								<div class="page">
									<div class="page__wrap <?php echo $pclass ?>">
										<p><?php echo $value["name"]; ?>詳細</p>
									</div>
								</div>
							</li>
						</ul>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
<?php
}
?>
