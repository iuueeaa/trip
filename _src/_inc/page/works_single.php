<?php setHtmlMv($this_page_value, 'p-mv__sub'); ?>

<?php
$sectionId = "main";
$sectionClass = "page-works__" . $sectionId;
$sectionValue = $this_page_value;
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
  <div class="section__wrap">
    <div class="<?php echo $sectionClass; ?>__wrap">
      <?php setHtmlBody($sectionValue['body'] ?? []); ?>
    </div>
  </div>
</section>
