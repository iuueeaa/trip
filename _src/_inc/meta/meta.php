<?php
// 前提: $meta配列と、$local_path, $root_path, $gf, などは _page.php 側でセット済み

// @done HMR-1: dev 判定を .ports.json 存在ベースに変更。$mode/$manifestJsonUrl 削除、本番は固定パス直書きに変更。

$gf = array_map(fn($font) => 'family=' . $font, $googlefont ?? []);

?>
<meta charset="utf-8">
<title><?= htmlspecialchars($meta['title']) ?></title>
<?php if (!isset($_GET["amp"])): ?>
  <link rel="canonical" href="<?= htmlspecialchars($meta['url']) ?>" />
<?php endif; ?>
<meta http-equiv="content-script-type" content="text/javascript">
<meta name="description" content="<?= htmlspecialchars($meta['description']) ?>">
<meta property="og:title" content="<?= htmlspecialchars($meta['title']) ?>">
<meta property="og:description" content="<?= htmlspecialchars($meta['description']) ?>">
<meta property="og:url" content="<?= htmlspecialchars($meta['url']) ?>">
<meta property="og:image" content="<?= htmlspecialchars($meta['ogimage']) ?>">
<meta property="og:site_name" content="<?= htmlspecialchars($meta['site_title']) ?>">
<meta property="og:type" content="<?= htmlspecialchars($meta['type']) ?>">
<meta name="robots" content="index,follow">
<meta name="twitter:card" content="<?= htmlspecialchars($meta['twittercard']) ?>">
<?php if (!empty($meta['twitteraccount'])): ?>
  <meta name="twitter:site" content="<?= htmlspecialchars($meta['twitteraccount']) ?>">
<?php endif; ?>
<?php if (!empty($meta['telephone'])): ?>
  <meta name="format-detection" content="telephone=no">
<?php endif; ?>
<?php if (!empty($meta['viewport'])): ?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<?php endif; ?>
<meta name="viewport-extra" content="width=device-width,initial-scale=1,min-width=375" />
<link rel="alternate" type="application/rss+xml" title="<?= htmlspecialchars($meta['title']) ?>フィード" href="<?= htmlspecialchars($meta['url']) ?>/feed/" />
<link rel="shortcut icon" href="<?= htmlspecialchars($meta['favicon']) ?>">
<link rel="apple-touch-icon" href="<?= htmlspecialchars($meta['apple_icon']) ?>">
<meta name="theme-color" content="<?= htmlspecialchars($meta['theme_color']) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?<?= implode('&', $gf) ?>&display=swap" rel="stylesheet">
<!-- <script>
    (function(d) {
        var config = {
                kitId: 'wcc7kov',
                scriptTimeout: 3000,
                async: true
            },
            h = d.documentElement,
            t = setTimeout(function() {
                h.className = h.className.replace(/\bwf-loading\b/g, "") + " wf-inactive";
            }, config.scriptTimeout),
            tk = d.createElement("script"),
            f = false,
            s = d.getElementsByTagName("script")[0],
            a;
        h.className += " wf-loading";
        tk.src = 'https://use.typekit.net/' + config.kitId + '.js';
        tk.async = true;
        tk.onload = tk.onreadystatechange = function() {
            a = this.readyState;
            if (f || a && a != "complete" && a != "loaded") return;
            f = true;
            clearTimeout(t);
            try {
                Typekit.load(config)
            } catch (e) {}
        };
        s.parentNode.insertBefore(tk, s)
    })(document);
</script> -->
<?php
// 必要: $root_path, $local_path, $protocol は _l-page 等で定義済み

// --- どの環境かをザックリ判定 ---
$inDocker = is_file('/.dockerenv'); // コンテナ内なら true
$portsFile = $root_path . '/.ports.json';
$vitePort = 5173;

if (is_file($portsFile)) {
  $ports = json_decode(file_get_contents($portsFile), true);
  if (!empty($ports['VITE_PORT'])) {
    $vitePort = $ports['VITE_PORT'];
  }
}

// 1) ホスト名の決め方：Dockerなら "今アクセスしてきたホスト(IP)"、ローカルなら gethostname()
if ($inDocker) {
  // 例: "192.168.1.20:3000" → "192.168.1.20"
  $hostHeader = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost';
  $viteHost   = preg_replace('/:\d+$/', '', $hostHeader);
} else {
  // ローカル直起動時は実機のホスト名でOK（あなたの実績に合わせる）
  $viteHost = gethostname();
}

$viteOrigin = $protocol . $viteHost . ':' . $vitePort;

// 2) モード判定：.ports.json の存在ベース
$isDev = is_file($root_path . '/.ports.json')
         || (defined('WP_ENV') ? WP_ENV === 'development' : getenv('WP_ENV') === 'development');

// 3) devなら Vite を注入、そうでなければ固定パスで読み込む
?>
<?php if ($isDev): ?>
  <script type="module" src="<?= $viteOrigin; ?>/@vite/client"></script>
  <script type="module" src="<?= $viteOrigin; ?>/_src/main.js"></script>
<?php else: ?>
  <link rel="stylesheet" href="<?= htmlspecialchars($local_path) ?>/assets/css/style.css">
  <script type="module" src="<?= htmlspecialchars($local_path) ?>/assets/js/index.js"></script>
<?php endif; ?>
