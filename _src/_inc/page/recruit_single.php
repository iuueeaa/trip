<?php setHtmlMv($this_page_value, 'p-mv__sub'); ?>
<?php
$sectionId = "index";
$sectionClass = "page-recruit" . $sectionId;
?>　
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap width__narrow">
			<?php $class = "p-recruit"; ?>
			<div class="<?php echo $class; ?>">
				<div class="<?php echo $class; ?>__wrap">
					<?php setHtmlTitle($this_page_value["title"], $class . "__title"); ?>
					<?php setHtmlBgImage($this_page_value["mv"]['image'], $class . '__image'); ?>
					<?php setHtmlText($this_page_value["mv"]["text"], $class . "__lead"); ?>
					<?php setHtmlRecruitTable($this_page_value['recruit'], $class . '__table',  true); ?>
					<div class="<?php echo $class . '__link'; ?>">
						<a href="<?php echo $link_path; ?>/recruit/" class="back"><span>一覧へ戻る</span></a>
						<a href="<?php echo $link_path . '/recruitform/?your-job=' .  $this_page_value["title"]["main"]; ?>" class="form"><span>応募フォームへ</span></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
