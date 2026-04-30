<?php
setHtmlMv($this_page_value, 'p-mv__sub');
?>

<?php
$sectionId = "index";
$sectionClass = "page-recruit__" . $sectionId;
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php
			$getList = getContentListArray($this_page_value);
			?>
			<ul class="<?php echo $sectionClass; ?>__ul">
				<?php foreach ($getList as $list) : ?>
					<li class="<?php echo $sectionClass; ?>__li">
						<?php $class = "p-recruit"; ?>
						<div class="<?php echo $class; ?>">
							<div class="<?php echo $class; ?>__wrap">
								<?php setHtmlTitle($list["title"], $class . "__title"); ?>
								<?php setHtmlBgImage($list['image'], $class . '__image'); ?>
								<?php setHtmlText($list["text"], $class . "__lead"); ?>

								<?php setHtmlRecruitTable($list['recruit'], $class . '__table',  false); ?>
								<div class="<?php echo $class . '__link'; ?>">
									<a href="<?php echo $list["link"]["link"]["url"]; ?>" class="detail"><span>詳細をみる</span></a>
									<a href="<?php echo $link_path . '/recruitform/?your-job=' . $list["title"]["main"]; ?>" class="form"><span>応募フォームへ</span></a>
								</div>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>
