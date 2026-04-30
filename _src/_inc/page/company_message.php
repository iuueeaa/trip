<?php setHtmlMv($this_page_value, 'p-mv__sub'); ?>


<?php
$sectionId = "message";
$sectionClass = $this_page_value['class'] . "__" . $sectionId;
$sectionValue = $this_page_value[$sectionId];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?> is-bg2">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php setHtmlTitle($sectionValue["title"], "p-title__sec", "h2"); ?>
			<?php
			$boxClass = 'p-box__yoko';
			$boxValue = $sectionValue['imageText'];
			?>
			<div class="<?php echo $boxClass; ?>">
				<div class="<?php echo $boxClass; ?>__wrap">
					<div class="<?php echo $boxClass; ?>__imageBox imageBox">
						<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
					</div>
					<div class="<?php echo $boxClass; ?>__textBox textBox">
						<div class="titleBox">
							<?php setHtmlTitle($boxValue['title'], 'p-title__sub', 'h3'); ?>
							<?php setHtmlText($boxValue['text'], 'p-text'); ?>
							<?php setHtmlLink($boxValue['link'], 'p-button'); ?>
						</div>
					</div>
				</div>
			</div>
			<?php setHtmlText($sectionValue["text"]); ?>
			<?php setHtmlSign($sectionValue["sign"]); ?>

		</div>
	</div>
</section>
