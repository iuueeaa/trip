#!/bin/bash

# WordPress 初期化スクリプト
# Docker コンテナ起動時に自動実行され、以下を設定：
# - WordPress 基本設定（URL、タイムゾーン、日付形式、パーマリンク）
# - プラグイン有効化
# - タクソノミーターム登録（sitemap.json から自動生成）
# - 固定ページ自動生成（sitemap.json の post_type === "page"）

WP="wp --allow-root --path=/var/www/html"
SITEMAP="/var/www/html/wp-content/themes/delaunay/assets/files/sitemap.json"
INITIALIZED_FLAG="/var/www/html/.wp-initialized"

# 初期化済みチェック（ファイルの中身で判定: 空=未初期化 / 中身あり=初期化済み）
if [ -s "$INITIALIZED_FLAG" ]; then
    echo "✅ 既に初期化済みです。スキップします。"
    exit 0
fi

# wp-cli 確認
if ! command -v wp &> /dev/null; then
    echo "❌ wp-cli が見つかりません。インストールしてください。"
    exit 1
fi

# WordPress が起動するまでポーリング待機
echo "⏳ WordPress 起動待機..."
MAX_WAIT=60
WAITED=0
until $WP core is-installed 2>/dev/null; do
    if [ "$WAITED" -ge "$MAX_WAIT" ]; then
        echo "❌ WordPress の起動タイムアウト（${MAX_WAIT}秒）"
        exit 1
    fi
    sleep 3
    WAITED=$((WAITED + 3))
done
echo "✅ WordPress 起動確認（${WAITED}秒待機）"

echo "🚀 WordPress 初期化開始..."

# 1️⃣  WordPress 基本設定
echo "📝 1️⃣  WordPress 基本設定..."
$WP option update siteurl 'http://localhost:9000'
$WP option update home 'http://localhost:9000'
$WP option update timezone_string 'Asia/Tokyo'
$WP option update date_format 'Y.m.d'
$WP option update posts_per_page '10'

# 2️⃣  パーマリンク設定
echo "📝 2️⃣  パーマリンク設定..."
$WP rewrite structure '/post-type/%postname%/'
$WP rewrite flush

# 3️⃣  プラグイン有効化
echo "📝 3️⃣  プラグイン有効化..."
$WP plugin activate advanced-custom-fields-pro 2>/dev/null || echo "⚠️  ACF Pro のアクティベートをスキップ（未インストールの可能性）"

# @done: sitemap.json をパースして投稿タイプとタクソノミーを自動登録

# 4️⃣  タクソノミーターム登録
echo "📝 4️⃣  タクソノミーターム登録..."

if [ ! -f "$SITEMAP" ]; then
    echo "⚠️  sitemap.json が見つかりません: $SITEMAP"
else
    php << PHPEOF
<?php
\$sitemap = json_decode(file_get_contents('$SITEMAP'), true);
if (!is_array(\$sitemap)) {
    echo "❌ sitemap.json のパースに失敗しました\n";
    exit(1);
}

function wp_cmd(string \$cmd): array {
    \$full = "wp --allow-root --path=/var/www/html " . \$cmd;
    exec(\$full . " 2>&1", \$output, \$ret);
    return [\$ret, implode("\n", \$output)];
}

function term_id_by_slug(string \$taxonomy, string \$slug): ?int {
    [\$ret, \$out] = wp_cmd("term get " . escapeshellarg(\$taxonomy) . " " . escapeshellarg(\$slug) . " --by=slug --field=term_id");
    if (\$ret === 0 && is_numeric(trim(\$out))) {
        return (int) trim(\$out);
    }
    return null;
}

foreach (\$sitemap as \$entry) {
    if (empty(\$entry['taxonomy']) || !is_array(\$entry['taxonomy'])) continue;
    \$cpt = \$entry['slug'];

    foreach (\$entry['taxonomy'] as \$tax) {
        \$tax_slug = \$cpt . '_' . \$tax['slug'];
        \$values   = \$tax['value'] ?? [];

        echo "  [タクソノミー: {\$tax_slug}]\n";
        \$slug_to_id = [];

        // Pass 1: 親ターム（parent 指定なし）
        foreach (\$values as \$item) {
            if (isset(\$item[2])) continue;
            \$name = \$item[0];
            \$slug = \$item[1];

            \$existing = term_id_by_slug(\$tax_slug, \$slug);
            if (\$existing !== null) {
                \$slug_to_id[\$slug] = \$existing;
                echo "    ✓ 既存: {\$name} (ID: {\$existing})\n";
                continue;
            }

            [\$ret, \$out] = wp_cmd("term create " . escapeshellarg(\$tax_slug) . " " . escapeshellarg(\$name) . " --slug=" . escapeshellarg(\$slug) . " --porcelain");
            if (\$ret === 0 && is_numeric(trim(\$out))) {
                \$slug_to_id[\$slug] = (int) trim(\$out);
                echo "    ✅ 作成: {\$name}\n";
            } else {
                echo "    ⚠️  失敗: {\$name} → {\$out}\n";
            }
        }

        // Pass 2: 子ターム（parent 指定あり）
        foreach (\$values as \$item) {
            if (!isset(\$item[2])) continue;
            \$name        = \$item[0];
            \$slug        = \$item[1];
            \$parent_slug = \$item[2];
            \$parent_id   = \$slug_to_id[\$parent_slug] ?? null;

            \$existing = term_id_by_slug(\$tax_slug, \$slug);
            if (\$existing !== null) {
                echo "    ✓ 既存: {\$name}\n";
                continue;
            }

            \$parent_opt = \$parent_id ? "--parent={\$parent_id}" : '';
            [\$ret, \$out] = wp_cmd("term create " . escapeshellarg(\$tax_slug) . " " . escapeshellarg(\$name) . " --slug=" . escapeshellarg(\$slug) . " {\$parent_opt} --porcelain");
            if (\$ret === 0) {
                echo "    ✅ 作成(子): {\$name}\n";
            } else {
                echo "    ⚠️  失敗(子): {\$name} → {\$out}\n";
            }
        }
    }
}
echo "✅ タクソノミーターム登録完了\n";
PHPEOF
fi

# @done: sitemap.json の post_type === "page" を抽出して固定ページを自動生成

# 5️⃣  固定ページ自動生成
echo "📝 5️⃣  固定ページ自動生成..."

if [ ! -f "$SITEMAP" ]; then
    echo "⚠️  sitemap.json が見つかりません: $SITEMAP"
else
    php << PHPEOF
<?php
\$sitemap = json_decode(file_get_contents('$SITEMAP'), true);
if (!is_array(\$sitemap)) {
    echo "❌ sitemap.json のパースに失敗しました\n";
    exit(1);
}

function wp_cmd(string \$cmd): array {
    \$full = "wp --allow-root --path=/var/www/html " . \$cmd;
    exec(\$full . " 2>&1", \$output, \$ret);
    return [\$ret, implode("\n", \$output)];
}

foreach (\$sitemap as \$entry) {
    if ((\$entry['post_type'] ?? '') !== 'page') continue;
    // slug が # や空、クエリパラメータを含むものはスキップ
    \$slug = \$entry['slug'] ?? '';
    if (empty(\$slug) || strpos(\$slug, '#') !== false || strpos(\$slug, '?') !== false) continue;

    \$name  = \$entry['name']    ?? \$slug;
    \$title = \$entry['name_en'] ?? \$name;

    // 既存チェック（slug でページを検索）
    [\$ret, \$out] = wp_cmd("post list --post_type=page --name=" . escapeshellarg(\$slug) . " --field=ID --post_status=publish,draft");
    if (\$ret === 0 && is_numeric(trim(\$out))) {
        echo "  ✓ 既存ページ: {\$name} (slug: {\$slug})\n";
        continue;
    }

    // ページ作成
    [\$ret, \$out] = wp_cmd(
        "post create --post_type=page --post_title=" . escapeshellarg(\$name) .
        " --post_name=" . escapeshellarg(\$slug) .
        " --post_status=publish --porcelain"
    );
    if (\$ret === 0 && is_numeric(trim(\$out))) {
        echo "  ✅ 作成: {\$name} (slug: {\$slug}, ID: " . trim(\$out) . ")\n";
    } else {
        echo "  ⚠️  失敗: {\$name} → {\$out}\n";
    }
}
echo "✅ 固定ページ自動生成完了\n";
PHPEOF
fi

# 初期化完了フラグに実行日時を書き込む（ホスト側の _wp/.wp-initialized に同期）
# 再初期化したい場合は、このファイルの中身を空にする: > _wp/.wp-initialized
echo "initialized at $(date '+%Y-%m-%d %H:%M:%S')" > "$INITIALIZED_FLAG"

echo "✅ WordPress 初期化完了！"
