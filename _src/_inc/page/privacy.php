<?php setHtmlMv($this_page_value, 'p-mv__noimage is-center'); ?>
<?php
$sectionId = "body";
$sectionClass = $this_page_value['class'] . "__" . $sectionId;
$sectionValue = $this_page_value[$sectionId];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?> is-narrow pt0">
	<div class="section__wrap">
		<?php setHtmlBody($sectionValue); ?>
	</div>
</section>
