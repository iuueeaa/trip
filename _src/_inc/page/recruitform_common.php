<?php setHtmlMv($this_page_value, 'p-mv__noimage is-center'); ?>
<?php
$sectionId = "form";
$sectionClass = $this_page_value["class"] . "__" . $sectionId;
$sectionValue = $this_page_value;
$formtype = $sectionValue['form']['type'];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
<div class="section__wrap">
	<div class="<?php echo $sectionClass; ?>__wrap">
		<div class="<?php echo $sectionClass; ?>__inner">
			<div class="<?php echo $sectionClass ?>__textBox">
				<?php
				if (!empty($sectionValue['form']["lead"])) {
					setHtmlText($sectionValue['form']["lead"], 'p-text');
				}
				if (!empty($sectionValue['form']["text"])) {
					setHtmlText($sectionValue['form']["text"], 'p-text');
				}
				if (!empty($sectionValue['form']["link"])) : ?>
					<div class="p-links">
						<div class="p-links__wrap">
							<?php setHtmlLink($sectionValue['form']["link"], 'p-button'); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<?php
			if ($formtype != "thanks") {
				setHtmlForm($sectionValue, $formtype, $sectionClass);
				if ($formtype == "confirm" && !empty($errm)) {
					echo $errm;
				}
			}
			?>
		</div>
		</div>
	</div>
</section>
