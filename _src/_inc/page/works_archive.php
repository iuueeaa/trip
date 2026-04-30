<?php
setHtmlMv($this_page_value, 'p-mv__sub');
?>

<?php
$sectionId = "index";
$sectionClass = "page-works__" . $sectionId;
$sectionValue = $this_page_value;
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
  <div class="section__wrap">
    <div class="<?php echo $sectionClass; ?>__wrap">
      <?php
      $blockClass = 'p-list__col3';
      $blockValue = getContentListArray($sectionValue);
      ?>
      <div class="<?php echo $blockClass; ?>">
        <div class="<?php echo $blockClass; ?>__wrap">
          <ul class="<?php echo $blockClass; ?>__ul">
            <?php foreach ($blockValue as $list) : ?>
              <li class="<?php echo $blockClass; ?>__li">
                <?php
                $boxClass = 'p-box__tate';
                $boxValue = $list;
                ['url' => $boxUrl, 'target' => $boxTarget] = SetBoxLink($boxValue['link']);
                ?>
                <div class="<?php echo $boxClass; ?>">
                  <div class="<?php echo $boxClass; ?>__wrap">
                    <a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
                      <?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
                    </a>
                    <div class="<?php echo $boxClass; ?>__textBox textBox">
                      <div class="info">
                        <?php setHtmlText($boxValue['date'], 'p-date'); ?>
                        <?php setHtmlTaxonomy($boxValue['taxonomy']['category'] ?? [], 'p-category', false, true); ?>
                      </div>
                      <a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
                        <?php setHtmlTitle($boxValue['title'], 'p-title__box', 'h2'); ?>
                        <?php setHtmlText($boxValue['text'], 'p-text'); ?>
                      </a>
                      <?php setHtmlLink($boxValue['link'], 'p-button is-color__sub', 'icon-arrow'); ?>
                    </div>
                  </div>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
          <?php
          setHtmlPagerFromPostList($sectionValue['postList']);
          ?>
        </div>
      </div>
    </div>
  </div>
</section>
