<?php
/* ------------------------------------------------
.b-list
------------------------------------------------ */
$blockClass = $sectionClass . '__list';
$blockValue = $sectionValue['xxx'];
?>
<div class="<?php echo $blockClass; ?>">
  <div class="<?php echo $blockClass; ?>__wrap">
    <ul class="<?php echo $blockClass; ?>__ul">
      <?php foreach ($blockValue as $list) : ?>
        <li class="<?php echo $blockClass; ?>__li">

        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>



<?php
/* ------------------------------------------------
.b-splide
------------------------------------------------ */
$blockClass = $sectionClass . '__list';
$blockValue = $sectionValue['xxx'];
$splideSetting = $sectionValue['xxx'];
$splideSetting = array(
  "type" => "loop",
  "drag" => "free",
  "perPage" => 4,
  "gap" => "2rem",
  "pagination" => false,
  "arrows" => false,
  "focus" => 1,
  "padding" => array("left" => 0, "right" => "0%"),
  "autoplay" => true,
  "focus" => 0,
  "interval" => 4000,
  "rewind" => true,
  "snap" => true,
  "autoScroll" => array(
    "speed" => 1,
    "rewind" => false,
    "pauseOnHover" => false,
    "pauseOnFocus" => false,
  ),
  "breakpoints" => array(
    "1000" => array("perPage" => 3),
    "680" => array("perPage" => 2)
  )
);
?>
<div class="<?php echo $blockClass; ?> js-splide splide" role="group" data-splide='<?php echo json_encode($splideSetting); ?>'>
  <div class="<?php echo $blockClass; ?>__wrap splide__track">
    <ul class="<?php echo $blockClass; ?>__ul splide__list">
      <?php foreach ($blockValue as $list) : ?>
        <li class="<?php echo $blockClass; ?>__li splide__slide">

        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <div class="splide__ctrl">
    <div class="splide__arrows">
      <button class="splide__arrow splide__arrow--prev"></button>
      <ul class="splide__pagination"></ul>
      <button class="splide__arrow splide__arrow--next"></button>
    </div>
  </div>
</div>


<?php
/* ------------------------------------------------
.b-box
------------------------------------------------ */
$boxClass = $sectionClass . '__box';
$boxValue = array(
  'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', "画像タイトル"),
  'date' => date($date_format),
  'taxonomy' => array(
    'category' => array(
      (object) ['name' => 'カテゴリ1', 'slug' => 'category1', 'taxonomy' => 'category'],
    ),
    'tag' => array(
      (object) ['name' => 'カテゴリ1', 'slug' => 'tag1', 'taxonomy' => 'tag'],
      (object) ['name' => 'カテゴリ2', 'slug' => 'tag2', 'taxonomy' => 'tag'],
      (object) ['name' => 'カテゴリ3', 'slug' => 'tag3', 'taxonomy' => 'tag'],
    ),
  ),
  'title' => setValueTitle('見出しが入ります', "Headline Title"),
  'lead' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
  'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
  'link' => array(
    'mode' => 'link',
    'link' => array(
      'title' => 'VIEW MORE',
      'url' => $link_path . "/",
      'target' => '',
    ),
    'file' => false, //pdfなどときはこちら(必須じゃない)
  ),
);
$boxUrl = SetBoxLink($boxValue['link'])['url'];
$boxTarget = SetBoxLink($boxValue['link'])['target'];
?>
<div class="<?php echo $boxClass; ?>">
  <div class="<?php echo $boxClass; ?>__wrap">
    <a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
      <?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
    </a>
    <div class="<?php echo $boxClass; ?>__textBox textBox">
      <div class="info">
        <?php setHtmlText($boxValue['date'], 'p-date'); ?>
        <?php setHtmlTaxonomy($boxValue['taxonomy']['category'], 'p-category', false, true); ?>
      </div>
      <a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
        <?php setHtmlTitle($boxValue['title'], 'p-title__sec', 'h2'); ?>
        <?php setHtmlText($boxValue['lead'], 'p-lead'); ?>
        <?php setHtmlText($boxValue['text'], 'p-text'); ?>
      </a>
      <?php setHtmlTaxonomy($boxValue['taxonomy']['tag'], 'p-tag', false, true); ?>
      <?php setHtmlLink($boxValue['link'], 'p-button is-color__sub', 'icon-arrow'); ?>
    </div>
  </div>
</div>

<?php
/* ------------------------------------------------
.b-news
------------------------------------------------ */
$boxClass = $sectionClass . '__news';
$boxValue = array(
  'taxonomy' => array(
    'category' => array(
      (object) ['name' => 'カテゴリ1', 'slug' => 'category1', 'taxonomy' => 'category'],
    ),
  ),
  'date' => date($date_format),
  'title' => setValueTitle('見出しが入ります', "Headline Title"),
  'link' => array(
    'mode' => 'link',
    'link' => array(
      'title' => 'VIEW MORE',
      'url' => $link_path . "/",
      'target' => '',
    ),
  ),
);
$boxUrl = SetBoxLink($boxValue['link'])['url'];
$boxTarget = SetBoxLink($boxValue['link'])['target'];
?>
<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>">
  <dl class="<?php echo $boxClass; ?>__dl">
    <dt class="<?php echo $boxClass; ?>__dt">
      <?php setHtmlText($boxValue['date'], 'p-date'); ?>
      <?php setHtmlTaxonomy($boxValue['taxonomy']['category'], 'p-category', false, true); ?>
    </dt>
    <dd class="<?php echo $boxClass; ?>__dd">
      <?php setHtmlTitle($boxValue['title'], 'p-title__sub', 'h2'); ?>
    </dd>
  </dl>
</a>


<?php
/*
固定ページの追加の仕方
個別のレイアウトにする場合下記の手順を実施すること。(xxxxはslug)
1. _wp/page-xxxx.phpにテンプレート名を入れる。
2. 該当のpage_valueのにsection_modeにslugを入力する。
3. _src/html/_inc/page/xxxx.phpを作成してレイアウトを追加する。
4. 以下のacfを設定して有効化する。
5. wpの画面で該当ページのテンプレートを指定する。
*/
