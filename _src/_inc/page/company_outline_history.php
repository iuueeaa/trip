<?php setHtmlMv($this_page_value, 'p-mv__sub'); ?>

<?php
$sectionId = "history";
$sectionClass = $this_page_value['class'] . "__" . $sectionId;
$sectionValue = $this_page_value[$sectionId];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php setHtmlTitle($sectionValue["title"], "p-title__sec", "h2"); ?>
			<?php setHtmlHistory($sectionValue["history"]); ?>
		</div>
	</div>
</section>
