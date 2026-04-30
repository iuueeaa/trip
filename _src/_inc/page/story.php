<?php setHtmlMv($this_page_value, 'p-mv__sub'); ?>

<?php
$sectionId = "main";
$sectionClass = $this_page_value['class'] . "__" . $sectionId;
$sectionValue = $this_page_value[$sectionId] ?? [];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
  <div class="section__wrap">
    <div class="<?php echo $sectionClass; ?>__wrap">
      <?php setHtmlTitle($sectionValue["title"] ?? null, "p-title__sec", "h2"); ?>
      <?php setHtmlText($sectionValue["text"] ?? null); ?>
    </div>
  </div>
</section>
