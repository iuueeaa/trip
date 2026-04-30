<!DOCTYPE html>
<?php
$image_path = 'assets/image/';
include("_value.php");
?>
<html dir="ltr" lang="ja">

<head>
  <meta charset="utf-8">
  <title><?php echo $clientname; ?> | <?php echo $taskname; ?></title>
  <meta name="robots" content="noindex,nofollow">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <link rel="shortcut icon" href="<?php echo $image_path ?>favicon.ico">
  <link rel="stylesheet" href="assets/css/style.css" media="screen,print">

</head>

<body>
  <div id="wrapAll">
    <div class="l-header">
      <div class="l-header__wrap">
        <div class="l-header__logo"><img src="<?php echo $image_path ?>logo.svg" alt="<?php echo $clientname; ?>"></div>
        <p class="l-header__credit">
          <span><?php echo $clientname; ?><br><?php echo $taskname; ?></span>
        </p>
      </div>
    </div>
    <div class="l-main">
      <div class="l-main__wrap">
        <div class="p-list">
          <ul class="p-list__ul">
            <?php foreach ($designs as $key => $value) : ?>
              <li class="p-list__li">
                <div class="p-list__wrap">
                  <div class="p-list__head">
                    <?php if (!empty($value['date'])) : ?>
                      <span class="date"><?php echo  $value['date']; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($value['title'])) : ?>
                      <span class="title"><?php echo  $value['title']; ?></span>
                    <?php endif; ?>
                  </div>
                  <div class="p-list__body">
                    <?php foreach ($value['list'] as $link) : ?>
                      <a href="<?php echo $link['path']; ?>" target="_blank">
                        <dl>
                          <dt><span><?php echo $link['dt']; ?></span></dt>
                          <?php if (!empty($link['dd'])) : ?>
                            <dd><span><?php echo $link['dd']; ?></span></dd>
                          <?php endif; ?>
                        </dl>
                        <?php if (strpos($link['path'], ".zip") !== false || strpos($link['path'], ".pdf") !== false || strpos($link['path'], ".webp") !== false || strpos($link['path'], ".webp") !== false): ?>
                          <svg class="dl" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
                            <path d="M480-336.92 338.46-478.46l28.31-28.77L460-414v-346h40v346l93.23-93.23 28.31 28.77L480-336.92ZM264.62-200q-27.62 0-46.12-18.5Q200-237 200-264.62v-96.92h40v96.92q0 9.24 7.69 16.93 7.69 7.69 16.93 7.69h430.76q9.24 0 16.93-7.69 7.69-7.69 7.69-16.93v-96.92h40v96.92q0 27.62-18.5 46.12Q723-200 695.38-200H264.62Z" />
                          </svg>
                        <?php else: ?>
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
                            <path d="m531.69-480-184-184L376-692.31 588.31-480 376-267.69 347.69-296l184-184Z" />
                          </svg>
                        <?php endif; ?>

                      </a>
                    <?php endforeach; ?>
                  </div>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
    <div class="l-footer">
      <div class="l-footer__wrap">
        <div class="l-footer__wrap">
          <div class="l-footer__qr">
            <dl>
              <dt id="qrcode"></dt>
              <dd><span>スマートフォンで確認する場合は、<br>こちらのQRコードを読み取ってください。</span></dd>
            </dl>
          </div>
          <!-- <a href="https://delaunay.jp/" target="_blank" class="l-footer__logo"><img src="<?php echo $image_path ?>my_logo.svg" alt="DELAUNAY Inc. | 株式会社ドロネー"></a> -->
          <div class="l-footer__copyright">© <?php echo date("Y"); ?> <a href="https://delaunay.jp/" target="_blank">DELAUNAY Inc.</a></div>
        </div>
      </div>
    </div>
  </div>
  <script src="assets/js/qrcode.min.js"></script>
  <script>
    window.onload = function() {
      document.body.classList.add("is-load");
      const url = window.location.href; // 現在のページのURLを取得
      const qrcode = new QRCode(document.getElementById("qrcode"), {
        text: url,
        width: 128,
        height: 128,
        colorDark: "#000000",
        colorLight: "rgba(0,0,0,0)"
      });
    };
  </script>
</body>
