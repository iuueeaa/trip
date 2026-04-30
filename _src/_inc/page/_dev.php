<?php include("_nav.php"); ?>


<?php
$sectionId = "test1";
$sectionClass = "page-xxxxx__" . $sectionId;
$testImage = setValueImage($image_path . '_dummy/pic-dummy_image.webp', 'テスト画像');
$testImage2 = setValueImage($image_path . '_dummy/pic-dummy_image.webp', 'テスト画像');
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="section <?php echo $sectionClass; ?>">
  <br><br><br><br><br><br><br><br><br><br>
  <div class="js-invert__switch" style="cursor: pointer;">js-invert__switch</div>
  <br><br><br><br>
  <a href="#Test4">アンカーリンク</a>
  <br><br><br><br><br><br><br><br><br><br>
  <br><br><br><br><br><br><br><br><br><br>
  <iframe
    width="560"
    height="315"
    src="https://www.youtube.com/embed/OYifjZ8jLQs"
    title="YouTube video player"
    frameborder="0"
    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
    allowfullscreen></iframe>
  <div
    class="js-youtube"
    data-id="OYifjZ8jLQs"
    data-ratio="56.25"
    data-auto="true">
    <!-- JSが内部に
       <div id="YT-XXXXXXXXXXX_0"></div>
       とカバー要素を自動で追加する -->
  </div>
  <br><br><br><br><br><br><br><br><br><br>
  <div class="js-video" data-src="<?php echo $local_path . '/assets/files/dummy.mp4' ?>">
    <div class="js-video__player">
      <video
        class="c-video"
        poster="<?php echo $image_path . '_dummy/pic-dummy_b.webp'; ?>"
        playsinline
        preload="none">
        <!-- JSが <source src="..."> を自動で追加 -->
      </video>
    </div>
  </div>
  <br><br><br><br><br><br><br><br><br><br><br>
  <div class="js-video" data-src="<?php echo $local_path . '/assets/files/dummy.mp4' ?>">
    <div class="js-video__player">
      <video
        class="c-video"
        playsinline
        muted
        autoplay
        loop
        preload="auto">
        <!-- JSが <source src="..."> を自動で追加 -->
      </video>
    </div>
  </div>
  <br><br><br><br><br><br><br><br><br>
  <a href="javascript:void(0);"
    class="js-modal__open"
    data-modal-type="inline"
    data-modal-target="#modalInline-about">
    詳細を見る
  </a>
  <div id="modalInline-about" class="js-modal__hidden">
    <div class="任意">
      <h2>タイトル</h2>
      <p>テキスト…</p>
    </div>
  </div>
  <br><br><br><br><br><br><br><br><br><br>
  <ul class="c-galleryThumbs">
    <li>
      <button type="button"
        class="js-modal__open"
        data-modal-type="gallery"
        data-gallery-target="#modalGallery-main"
        data-gallery-index="0">
        画像１
        <!-- <img src="<?php echo $image_path . '_dummy/pic-dummy.webp'; ?>" alt=""> -->
      </button>
    </li>
    <li>
      <button type="button"
        class="js-modal__open"
        data-modal-type="gallery"
        data-gallery-target="#modalGallery-main"
        data-gallery-index="1">
        画像２
        <!-- <img src="<?php echo $image_path . '_dummy/pic-dummy_b.webp'; ?>" alt=""> -->
      </button>
    </li>
  </ul>

  <div id="modalGallery-main" class="js-modal__hidden">
    <div class="c-modalGallery">
      <div class="splide js-modalGallery" data-modal-gallery="main">
        <div class="splide__track">
          <ul class="splide__list">
            <li class="splide__slide">
              <img src="<?php echo $image_path . '_dummy/pic-dummy.webp'; ?>" alt="">
            </li>
            <li class="splide__slide">
              <img src="<?php echo $image_path . '_dummy/pic-dummy_b.webp'; ?>" alt="">
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <br><br><br><br><br><br><br><br><br><br><br><br>
  <button type="button"
    class="js-modal__open"
    data-modal-type="youtube"
    data-youtube-id="OYifjZ8jLQs"
    data-youtube-title="動画のタイトル">
    youtubeを見る
  </button>
  <br><br><br><br><br><br><br><br><br><br><br><br>
  <button type="button"
    class="js-modal__open"
    data-modal-type="video"
    data-video-src="<?php echo $local_path . '/assets/files/dummy.mp4' ?>"
    data-video-poster="<?php echo $image_path . '_dummy/pic-dummy_b.webp'; ?>"
    data-video-title="動画のタイトル">
    videoを見る

  </button>
  <br><br><br><br><br><br><br><br><br><br><br><br>
  <br><br><br><br><br><br><br><br><br><br><br><br>
  <br><br><br><br><br><br><br><br><br><br><br><br>
  <br><br><br><br><br><br><br><br><br><br><br><br>
  <br><br><br><br><br><br><br><br><br><br><br><br>



  <div style="width: 300px;">
    <?php setHtmlBgImage($testImage, 'p-image'); ?>
  </div>
  <br><br><br><br><br><br><br><br><br><br>
  <br><br><br><br><br><br><br><br><br><br>
</section>

<?php
$sectionId = "test2";
$sectionClass = "page-xxxxx__" . $sectionId;
$splideSetting = array(
  "type" => "loop",
  "drag" => false,
  "perPage" => 2,
  "gap" => "0rem",
  "pagination" => false,
  "arrows" => false,
  "padding" => "10%",
  "focus" => 0,
  "interval" => 4000,
  "rewind" => true,
  "snap" => true,
  "autoScroll" => array(
    "speed" => 0.5,
    "rewind" => false,
    "pauseOnHover" => false,
    "pauseOnFocus" => false,
  ),
  "breakpoints" => array(
    "1000" => array(
      "perPage" => 2,
      "padding" => "0%",
    ),
    "680" => array(
      "perPage" => 1,
      "padding" => "5%",
    )
  )
);
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="section <?php echo $sectionClass; ?> is-bg2 is-invert">
  <br><br><br><br><br><br><br><br><br><br>
  <div style="width: 100px; height: 100px; background-color: red;" class="js-sa__op"></div>
  <!-- <div class="js-invert__switch" style="cursor: pointer;">js-invert__switch</div> -->
  <br><br><br><br><br><br><br><br><br><br>
  <br><br><br><br><br><br><br><br><br><br>
  <br><br><br><br><br><br><br><br><br><br>
  <div class="js-splide splide" role="group" data-splide='<?php echo json_encode($splideSetting); ?>'>
    <div class="splide__track">
      <ul class="splide__list">
        <li class="splide__slide"><img src="<?php echo $image_path . '_dummy/pic-dummy.webp'; ?>" alt=""></li>
        <li class="splide__slide"><img src="<?php echo $image_path . '_dummy/pic-dummy_b.webp'; ?>" alt=""></li>
        <li class="splide__slide"><img src="<?php echo $image_path . '_dummy/pic-dummy.webp'; ?>" alt=""></li>
        <li class="splide__slide"><img src="<?php echo $image_path . '_dummy/pic-dummy_b.webp'; ?>" alt=""></li>
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
  <br><br><br><br><br><br><br><br><br><br>
  <br><br><br><br><br><br><br><br><br><br>
</section>

<?php
$sectionId = "test3";
$sectionClass = "page-xxxxx__" . $sectionId;
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="section <?php echo $sectionClass; ?> is-bg3">
  <br><br><br><br><br><br><br><br><br><br>
  <!-- <div class="js-invert__switch" style="cursor: pointer;">js-invert__switch</div> -->
  <br><br><br><br><br><br><br><br><br><br>
  <div style="width: 300px;">
    <?php setHtmlBgImage($testImage, 'p-image'); ?>
  </div>
  <div class="box js-sa"></div>
  <br><br><br><br><br><br><br><br><br><br>
  <br><br><br><br><br><br><br><br><br><br>
  <div style="width: 500px;">
    <div class="js-slide" data-arrow="true" data-dots="true" data-interval="6000">
      <ul class="js-slide__ul">
        <li class="js-slide__li"><?php setHtmlBgImage($testImage, 'p-image'); ?></span></li>
        <li class="js-slide__li"><?php setHtmlBgImage($testImage2, 'p-image'); ?></span></li>
        <li class="js-slide__li"><?php setHtmlBgImage($testImage, 'p-image'); ?></span></li>
        <li class="js-slide__li"><?php setHtmlBgImage($testImage2, 'p-image'); ?></span></li>
      </ul>
    </div>
  </div>
  <br><br><br><br><br><br><br><br><br><br>
  <br><br><br><br><br><br><br><br><br><br>
  <a href="#">ページ先頭に戻る</a>
</section>


<?php
$sectionId = "test4";
$sectionClass = "page-xxxxx__" . $sectionId;
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="section <?php echo $sectionClass; ?> js-invertArea">
  <br><br><br><br><br><br><br><br><br><br>
  <!-- <div class="js-invert__switch" style="cursor: pointer;">js-invert__switch</div> -->
  <br><br><br><br><br><br><br><br><br><br>
  <br><br><br><br><br><br><br><br><br><br>
  <br><br><br><br><br><br><br><br><br><br>
  <br><br><br><br><br><br><br><br><br><br>
  <div class="js-tab">
    <div class="js-tab__wrap">
      <div class="js-tab__head">
        <ul>
          <li><a href="javascript:void(0);" class="js-tab__nav is-active" data-tab="tab1"><span>Tab 01</span></a></li>
          <li><a href="javascript:void(0);" class="js-tab__nav" data-tab="tab2"><span>Tab 02</span></a></li>
          <li><a href="javascript:void(0);" class="js-tab__nav" data-tab="tab3"><span>Tab 03</span></a></li>
          <li><a href="javascript:void(0);" class="js-tab__nav" data-tab="tab4"><span>Tab 04</span></a></li>
        </ul>
      </div>
      <div class="js-tab__body">
        <div class="js-tab__body__wrap">
          <div class="js-tab__content is-active" data-tab="tab1">
            <div class="js-tab__content__wrap">
              <span>Content 01<br>This text is a dummy. It is included to check the size, amount, spacing, and line spacing of the text.</span>
            </div>
          </div>
          <div class="js-tab__content" data-tab="tab2">
            <div class="js-tab__content__wrap">
              <span>Content 02<br>This text is a dummy. It is included to check the size, amount, spacing, and line spacing of the text.</span>
            </div>
          </div>
          <div class="js-tab__content" data-tab="tab3">
            <div class="js-tab__content__wrap">
              <span>Content 03<br>This text is a dummy. It is included to check the size, amount, spacing, and line spacing of the text.This text is a dummy. It is included to check the size, amount, spacing, and line spacing of the text.This text is a dummy. It is included to check the size, amount, spacing, and line spacing of the text.</span>
            </div>
          </div>
          <div class="js-tab__content" data-tab="tab4">
            <div class="js-tab__content__wrap">
              <span>Content 04<br>This text is a dummy. It is included to check the size, amount, spacing, and line spacing of the text.</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <br><br><br><br><br><br><br><br><br><br>
  <div class="js-accordion">
    <div class="js-accordion__head"><span>xxxxxxxxx</span></div>
    <div class="js-accordion__body"><span>xxxxxxxxx</span></div>
  </div>
  <br><br><br><br><br><br><br><br><br><br>
  <div class="js-map" data-lat="35.326968" data-lng="139.436861" data-pin="<?php echo $image_path; ?>common/pin.svg">
    <div class="js-map__wrap"></div>
  </div>
  <br><br><br><br>

</section>


<?php
$sectionId = "test5";
$sectionClass = "page-xxxxx__" . $sectionId;
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="section <?php echo $sectionClass; ?> is-bg3">
  <br><br><br><br><br><br><br><br><br><br>
  <div class="js-invert__switch" style="cursor: pointer;">js-invert__switch</div>
  <br><br><br><br><br><br><br><br><br><br>
  <br><br><br><br><br><br><br><br><br><br>
  <br><br><br><br><br><br><br><br><br><br>
  <br><br><br><br><br><br><br><br><br><br>
  <a href="#">ページ先頭に戻る</a>
  <br><br><br><br><br><br><br><br><br><br>
</section>

<!-- <?php
      $sectionId = "template";
      $sectionClass = "page-xxxxx__" . $sectionId;
      $sectionValue = $sectionTemplate;
      ?>
<section id="<?php echo ucfirst($sectionId); ?>" class="section <?php echo $sectionClass; ?>">
  <div class="section__wrap <?php echo $sectionClass; ?>__wrap">
    <div class="section__inner <?php echo $sectionClass; ?>__inner">
      <div class="<?php echo $sectionClass; ?>__xxx">
        <div class="<?php echo $sectionClass; ?>__xxx__wrap">
          <div class="<?php echo $sectionClass; ?>__xxx__imageBox">
            <?php setHtmlBgImage($sectionValue['image'], 'p-image'); ?>
          </div>
          <div class="<?php echo $sectionClass; ?>__xxx__textBox">
            <?php setHtmlTitle($sectionValue["title"], "p-title__sec", "h2"); ?>
            <?php setHtmlText($sectionValue['lead'], 'p-lead'); ?>
            <?php setHtmlText($sectionValue['text'], 'p-text'); ?>
            <?php setHtmlLink($sectionValue['link'], 'p-button'); ?>
          </div>
        </div>
      </div>
      <?php setHtmlBgImage($sectionValue['image'], 'p-image'); ?>
      <?php setHtmlTitle($sectionValue["title"], "p-title__sec", "h2"); ?>
      <?php setHtmlText($sectionValue['lead'], 'p-lead'); ?>
      <?php setHtmlText($sectionValue['text'], 'p-text'); ?>
      <?php setHtmlLink($sectionValue['link'], 'p-button'); ?>
      <?php
      $listClass = $sectionClass . '__list b-list__col3';
      $listValue = $sectionValue['list'];
      ?>
      <div class="<?php echo $listClass; ?>">
        <div class="<?php echo $listClass; ?>__wrap">
          <ul class="<?php echo $listClass; ?>__ul">
          <?php foreach ($listValue as $box) : ?>
            <?php
            $boxClass = $sectionClass . '__box';
            $boxValue = $box;
            ['url' => $boxUrl, 'target' => $boxTarget, 'title' => $boxTitle] = SetBoxLink($boxValue['link']);
            ?>
            <li class="<?php echo $listClass; ?>__li">
              <div class="<?php echo $boxClass; ?>">
                <div class="<?php echo $boxClass; ?>__wrap">
                  <div class="<?php echo $boxClass; ?>__imageBox">
                    <?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
                  </div>
                  <div class="<?php echo $boxClass; ?>__textBox">
                    <?php setHtmlTitle($boxValue["title"], "p-title__xxxx", "h3"); ?>
                    <?php setHtmlText($boxValue['lead'], 'p-lead'); ?>
                    <?php setHtmlText($boxValue['text'], 'p-text'); ?>
                    <?php setHtmlLink($boxValue['link'], 'p-button'); ?>
                  </div>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <?php
      $listClass = $sectionClass . '__list b-list__col3';
      $listValue = $sectionValue['list'];
      ?>
      <div class="<?php echo $listClass; ?>">
        <div class="<?php echo $listClass; ?>__wrap">
          <ul class="<?php echo $listClass; ?>__ul">
          <?php foreach ($listValue as $box) : ?>
            <?php
            $boxClass = $sectionClass . '__box';
            $boxValue = $box;
            ['url' => $boxUrl, 'target' => $boxTarget, 'title' => $boxTitle] = SetBoxLink($boxValue['link']);
            ?>
            <li class="<?php echo $listClass; ?>__li">
              <div class="<?php echo $boxClass; ?>">
                <div class="<?php echo $boxClass; ?>__wrap">
                  <a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" data-linktext="<?php echo $boxTitle; ?>" class="<?php echo $boxTitle; ?>__imageBoxLink">
                    <?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
                  </a>
                  <a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__textBoxLink">
                    <?php setHtmlTitle($boxValue["title"], "p-title__xxxx", "h3"); ?>
                    <?php setHtmlText($boxValue['lead'], 'p-lead'); ?>
                    <?php setHtmlText($boxValue['text'], 'p-text'); ?>

                  </a>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <div class="<?php echo $sectionClass; ?>__head"></div>
      <div class="<?php echo $sectionClass; ?>__body"></div>
      <div class="<?php echo $sectionClass; ?>__foot"></div>
    </div>
  </div>
</section>
 -->
