<?php setHtmlMv($this_page_value, 'p-mv__noimage is-center'); ?>

<?php
$sectionId = "faq";
$sectionClass = "page-faq__" . $sectionId;
$sectionValue = $this_page_value[$sectionId];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?> is-bg2 is-narrow">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php setHtmlFaq($sectionValue["dl"]); ?>
		</div>
	</div>
</section>
