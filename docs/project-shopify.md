# Shopify プロジェクト固有ルール

このファイルは CLAUDE.md の共通ルールを補完する。
Shopify + Liquid 構成のプロジェクトでのみ適用。

---

## ファイル構造とビルドフロー

Shopify テーマ標準構造:

```
theme/
├── assets/        # CSS / JS / 画像(編集対象)
├── config/        # settings_schema.json / settings_data.json
├── layout/        # theme.liquid 等のレイアウト
├── locales/       # 多言語ファイル
├── sections/      # セクション(編集可能ブロック)
├── snippets/      # 再利用パーツ
└── templates/     # ページテンプレート
```

ビルド/デプロイは Shopify CLI(`shopify theme dev` / `shopify theme push`)を使用。

---

## 仕様の正本

実装・修正時は以下を参照する:

- `sections/*.liquid` — セクション定義(schema 付き)
- `snippets/*.liquid` — 再利用パーツ
- `templates/*.json` / `templates/*.liquid` — ページテンプレート
- `config/settings_schema.json` — テーマ設定スキーマ
- `locales/*.json` — 翻訳キー

---

## Liquid テンプレート命名規則

- `sections/` — `kebab-case.liquid`(例: `hero-banner.liquid`)
- `snippets/` — `kebab-case.liquid`(例: `product-card.liquid`)
- スキーマ内の `name` は日本語可、`type` / `id` は英小文字 + ハイフン

---

## 共通コンポーネント / Snippets

プロジェクトで共通利用する snippet はリポジトリ起動時に列挙する。
新規 snippet を作成したら本セクションに追記。

---

## プロジェクト固有プロンプト

### 新規セクション追加

```
「○○」セクションを追加したいです。
1. sections/○○.liquid を作成
2. schema を定義(settings / blocks)
3. テンプレート(templates/*.json)に組み込み箇所を提案
```

### 新規 snippet 追加

```
「○○」snippet を追加したいです。
1. snippets/○○.liquid を作成
2. include 引数の仕様を提示
3. 呼び出し例を提案
```

### テーマカスタマイズ

```
config/settings_schema.json に「○○」設定を追加したいです。
1. 設定キーの命名提案
2. 関連する Liquid 側の参照箇所
```

---

## デバッグ

- `shopify theme dev` でローカルプレビュー
- ブラウザ DevTools で Liquid 変数(`{{ data | json }}`)を確認
- Shopify 管理画面 → テーマ → コードを編集 で本番差分確認

---

## PHP → Shopify テーマ変換マニュアル

このセクション以降は、カスタムPHPサイトをShopify(Dawn OS 2.0ベース)に移行する作業の詳細リファレンス。

> 対象: カスタムPHPサイトをShopify(Dawn OS 2.0ベース)に移行する作業
> 読み手: Claude Opus / Sonnet / Codex
> 更新日: 2026-03-25

---

## 1. 作業の全体フロー

```
1. 準備
   ├── Shopify CLI 確認（shopify version）
   ├── 開発ストア用意
   ├── Dawn テーマ取得（shopify theme init）
   ├── shopify.theme.toml の store を対象ストアに設定（なければ新規作成）
   └── 既存PHPソースの構造把握

2. テーマ骨格の構築
   ├── Dawn ファイルをリポジトリに配置
   ├── Vite 設定変更（出力先を assets/ に）
   ├── package.json スクリプト更新
   ├── .gitignore / .shopifyignore 作成
   └── 不要な Dawn ファイル削除（後述）

3. レイアウト変換
   ├── layout/theme.liquid にカスタムCSS/JS読み込み追加
   ├── sections/header.liquid 作成（PHPヘッダーから変換）
   ├── sections/footer.liquid 作成（PHPフッターから変換）
   ├── header-group.json / footer-group.json 更新
   └── 共通スニペット生成（後述・必須）

4. ページテンプレート変換
   ├── sections/d-*.liquid 作成（各PHPページテンプレから変換）
   ├── templates/*.json 作成・更新
   └── config/settings_data.json にブランドカラー設定

5. 動作確認
   ├── npm run build（Vite ビルド）
   ├── SVG sprite 生成
   ├── shopify theme dev で表示確認
   └── shopify theme push でストアにアップロード

6. 管理画面設定
   ├── メニュー作成（Navigation）
   ├── コレクション作成
   ├── ブログ作成
   ├── 固定ページ作成 + テンプレート割り当て
   ├── メタオブジェクト / メタフィールド定義
   └── テーマエディタで画像・テキスト設定
```

---

## 2. 変換時の注意事項

### 2.1 Shopify の制約

| 制約                                                          | 対応策                                                            |
| ------------------------------------------------------------- | ----------------------------------------------------------------- |
| `assets/` はフラット構造（サブディレクトリ不可）              | Vite 出力を `{案件名}.css` / `{案件名}.js` のように単一ファイルに |
| Shopify は SCSS を直接サポートしない                          | ビルド済み CSS を `assets/` に配置                                |
| `link_list` 型の schema 設定に `default` は使えない           | `*-group.json` 側で初期メニューを指定                             |
| テーマエディタのブロックは全商品共通                          | 商品ごとに違う内容 → メタオブジェクト + リスト型メタフィールド    |
| メニューに title/subtitle の概念がない                        | タイトルを `日本語\|English` 形式で入力、Liquid で split          |
| メタフィールド/メタオブジェクトの定義はテーマファイルから不可 | 管理画面 or API で作成                                            |
| `shopify theme dev` は `.gitignore` を見ない                  | `.shopifyignore` を使う                                           |
| `render` タグのパラメータ内でフィルターチェーンが使えない     | 事前に `assign` で変数に入れてから渡す（後述 2.2）                |
| 個別アイコンスニペット（`icon-xxx.liquid`）を作らない         | `d-icon.liquid`（sprite方式）に統一する（後述 2.2）               |

### 2.2 Liquid の落とし穴（必ず守ること）

#### render タグのパラメータにフィルターは使えない

```liquid
{%- comment -%} NG: append が無視され class に header_class の値だけ渡る {%- endcomment -%}
{%- render 'd-nav', menu_handle: 'main-menu', class: header_class | append: '__nav' -%}

{%- comment -%} OK: 事前に assign してから渡す {%- endcomment -%}
{%- assign nav_class = header_class | append: '__nav' -%}
{%- render 'd-nav', menu_handle: 'main-menu', class: nav_class -%}
```

**これは `render` の全パラメータに適用される。** `| default:`, `| append:`, `| prepend:` 等を使いたい場合は必ず事前に `assign` する。

#### アイコンは全て d-icon（sprite）経由にする

個別のアイコンスニペット（`icon-caret.liquid`, `icon-arrow.liquid` 等）は**作成しない**。
全て `d-icon.liquid` + `sprite.svg` で統一する。

```liquid
{%- comment -%} NG: 個別スニペットを作る {%- endcomment -%}
{% render 'icon-instagram' %}
{% render 'icon-caret' %}

{%- comment -%} OK: sprite 経由 {%- endcomment -%}
{% render 'd-icon', path: 'sns-instagram' %}
{% render 'd-icon', path: 'icon-arrow1' %}
```

`path` の値は `_src/icon/` 内の SVGファイル名（拡張子なし）と一致させる。
変換時に `setHtmlSvg('icon-name')` を見つけたら、`_src/icon/icon-name.svg` の存在を確認してから `d-icon` に変換すること。

#### SVG を `<img>` タグに勝手に変えない

**PHP版が `setHtmlSvg()` で SVG sprite 参照しているものは、全て `d-icon` で sprite 参照に変換する。**
`<img src="xxx.svg">` に変換してはいけない。

理由：

- CSSが `svg` 要素に対してスタイルを当てている（`fill`, `width`, `height`, `position` 等）
- `<img>` タグにすると CSS が効かず表示が崩れる
- ロゴ（`logo`, `logo_mark`, `logo_vertical`）も SVG sprite に含まれている

```liquid
{%- comment -%} NG: img タグに変える {%- endcomment -%}
<span class="p-logo">
  <img src="{{ 'logo.svg' | asset_url }}" alt="{{ shop.name }}">
  <span>{{ shop.name }}</span>
</span>

{%- comment -%} OK: sprite 経由（PHP版と同じ出力） {%- endcomment -%}
<span class="p-logo">
  {%- render 'd-icon', path: 'logo' -%}
  <span>{{ shop.name }}</span>
</span>
```

PHP版の `setHtmlLogo($icon, $class, $title)` の出力は：

```html
<span class="{$class}">
  <svg><use xlink:href="sprite.svg#{$icon}"></use></svg>
  <span>{$title}</span>
</span>
```

これと完全に一致させること。`<img>` は使わない。

#### Dawn の CSS/JS を残さない

theme.liquid の `<head>` には Dawn の CSS が3層入っている。{案件名}.css と競合するので全て削除する：

1. **`{% style %}` ブロック** — Dawn のフォント・カラー変数・body スタイル → **削除**
2. **`base.css`** — Dawn のリセットCSS → **削除**
3. **`component-*.css`** — Dawn のコンポーネントCSS → **削除**

{案件名}.css にリセットCSS含め全スタイルが入っているため、Dawn の CSS は不要。

残すもの：

- `{{ content_for_header }}` — Shopify必須（絶対に削除しない）
- `{% render 'meta-tags' %}` — SEO用
- `window.routes` / `window.cartStrings` 等 — カート機能で使用

#### theme.liquid の `<body>` 構造（PHP版 `_l-page.php` と一致させる）

PHP版の body 構造:

```html
<body class="is-ready js-sa">
  <div id="Guide">
    <span><span></span></span>
  </div>
  <div id="wrapAll">
    <!-- header -->
    <main class="l-main">
      <!-- content -->
    </main>
    <!-- footer -->
  </div>
</body>
```

Shopify版の対応:

```liquid
<body class="is-ready js-sa">
  <div id="Guide"><span><span></span></span></div>
  <div id="wrapAll">
    {% sections 'header-group' %}
    <main id="MainContent" class="l-main content-for-layout focus-none" role="main" tabindex="-1">
      {{ content_for_layout }}
    </main>
    {% sections 'footer-group' %}
  </div>
</body>
```

**必ず確認するポイント:**

- `<main>` に `class="l-main"` が含まれていること（Dawn デフォルトには無い）
- `<div id="Guide">` が body 直後、wrapAll の前にあること
- `l-loading` は入れない（PHP版でコメントアウト済み）
- Dawn の `skip-to-content-link` は入れない（PHP版に存在しない）
- Dawn の `cart-drawer` render は入れない（PHP版に存在しない）
- `l-header__pankuzu` には `is-narrow` クラスを付けること

#### PHPのコメントアウト箇所は変換しない

PHP ソースでコメントアウトされているコードは Liquid に含めない。

```php
// NG: これをLiquidに変換してしまう
<?php //include($inc_path . "_l-loading.php"); ?>

// OK: コメントアウトされているので無視する
```

**確認方法:** `//`, `/* */`, `<?php // ?>` でコメントアウトされた include / 関数呼び出しを
検出した場合、その機能は PHP 版でも無効化されている。Liquid に含めない。

#### setHtmlSvg の第2引数（class）を見落とさない

PHP の `setHtmlSvg($path, $class)` は第2引数で SVG 要素に class を付与する。
d-icon の `addclass` パラメータに対応する。

```php
// PHP版
setHtmlSvg('icon-block');                    // class なし
setHtmlSvg('icon-check1', 'is-check');       // class="is-check"
setHtmlSvg('logo_mark', 'is-logo');          // class="is-logo"
```

```liquid
{%- comment -%} Liquid版 {%- endcomment -%}
{%- render 'd-icon', path: 'icon-block' -%}
{%- render 'd-icon', path: 'icon-check1', addclass: 'is-check' -%}
{%- render 'd-icon', path: 'logo_mark', addclass: 'is-logo' -%}
```

**第2引数がある場合は必ず addclass に渡す。** 見落とすと CSS が効かない。

#### 複雑なコンポーネントはPHP版の実際のHTML出力を確認する

PHPソースコードだけでは最終的なHTML出力が読み取りにくい場合がある
（条件分岐、ネストした関数呼び出し、ループ内のHTML等）。

**以下のコンポーネントは、PHPの開発環境（`php -S` or Docker）で実際にページを開き、
ブラウザの開発者ツールで出力HTMLを確認してからLiquidに変換する:**

- `p-colorChanger` — ネストが深く、icon の class 指定が複雑
- `setHtmlNav` — display フィルタ、子メニュー、icon 分岐がある
- `setHtmlForm` — confirm/thanks の状態分岐
- `setHtmlBody` — ブロックタイプ別の出力分岐

PHPソースの読み取りだけで変換すると、構造の取り違えが起きる。
**推測で変換せず、実際の出力を確認する。**

#### Shopify のセクションラッパーに注意

Shopify は各セクションを自動的に `<div class="shopify-section">` でラップする。
PHP版にはないこの余分な div が CSS セレクタに影響する場合がある。

```html
<!-- PHP版 -->
<div id="wrapAll">
  <header class="l-header">...</header>

  <!-- Shopify版（実際の出力） -->
  <div id="wrapAll">
    <div id="shopify-section-header" class="shopify-section {案件名}-header-section">
      <header class="l-header">...</header>
    </div>
  </div>
</div>
```

通常は `.l-header` のセレクタは深い階層でも効くので問題ないが、
`>` （直接子要素セレクタ）や `+` （隣接兄弟セレクタ）を使っている SCSS がある場合は
CSS 調整が必要になる。変換後に確認すること。

### 2.3 変換の基本原則（必ず守ること）

#### 2.3.0 変換手順（この順番で作業する）

```
Step 1: PHP版のブラウザ出力HTMLをコピー
        → 開発者ツール → Elements → 該当セクションを Copy outerHTML
        → php_output.html として保存

Step 2: PHP版のHTML内の全 class名 をリストアップ
        → SCSS で定義されている class を確認
        → 独自の class 名を勝手に作らないこと

Step 3: PHP ヘルパー関数の出力を正確に把握
        → setHtmlText, setHtmlTitle, setHtmlSvg 等の実際の出力HTML
        → _inc/function/parts/*.php と _inc/function/blocks/*.php を参照

Step 4: Liquid を書く
        → 既存スニペット（{案件名}-image, {案件名}-title 等）を使う
        → PHP版にないラッパーや class を追加しない

Step 5: Liquid の出力HTMLとPHP版を diff で比較
        → ブラウザ開発者ツールで Liquid の出力HTMLをコピー
        → php_output.html と比較して差分がないことを確認

Step 6: チェックリスト（2.3.8）で最終確認
```

**Step 1 と Step 5 が最も重要。** PHP版のHTML出力を見ずに変換すると、必ず構造が不一致になる。

#### 2.3.1 HTML/class構造を正確に引き継ぐ（最重要）

**PHP版のHTML出力と完全に一致するLiquidを書くこと。** これが最も重要なルール。
CSS/JSが既存のclass名・HTML構造に依存しているため、不一致があるとスタイル崩れや動作不良の原因になる。

**変換前に必ず PHP版の実際のHTML出力を確認する。** PHPソースだけでは最終出力が読み取りにくい場合がある。
ブラウザの開発者ツールで出力HTMLを確認してからLiquidに変換する。

```php
// PHP版
<div class="b-item__radio">
  <label for="type-0">
    <input type="radio" name="options[タイプ]" ...>
    <span><span class="text">ライオン</span></span>
  </label>
</div>
```

```liquid
{%- comment -%} OK: PHP版と同じ構造・class名 {%- endcomment -%}
<div class="b-item__radio">
  <label for="type-0">
    <input type="radio" name="options[タイプ]" ...>
    <span><span class="text">ライオン</span></span>
  </label>
</div>

{%- comment -%} NG: 構造やclass名を勝手に変える {%- endcomment -%}
<li class="b-item__option__li">
  <label class="b-item__option__label">
    <input type="radio" class="b-item__option__input">
    <span class="b-item__option__text">ライオン</span>
  </label>
</li>
```

**よくある間違い（過去の実例）:**

| 間違い                                            | 正しい対応                                                                           |
| ------------------------------------------------- | ------------------------------------------------------------------------------------ |
| `<span>` タグの省略                               | PHP版に `<span>` があれば必ず入れる。CSSが `span` にスタイルを当てている             |
| ラッパー div の省略（`<div class="p-input">` 等） | PHP版のラッパー要素は全て残す                                                        |
| class名の「Shopifyっぽい」リネーム                | PHP版の class名をそのまま使う。リネームしない                                        |
| `data-color` を `b-sticky` の div に配置          | **`<section>` タグに配置する**。JSがセクション単位で色を認識する                     |
| `data-splide` を Liquid 変数経由で出力            | **シングルクォートで JSON を直書き**する。`{{ var }}` 経由だとダブルクォートが壊れる |
| `style="background-image: url()"` で画像直書き    | **{案件名}-image スニペット経由**で出力する（lazy load が効かなくなる）              |
| `{案件名}-text` で WYSIWYG の出力を渡す           | WYSIWYG は `<p>` を含むので `<div class="p-text">{{ content }}</div>` で直接出力     |

#### 2.3.2 value/\*.php を参照してデータ設計する

PHP版の `_src/_inc/value/page/*.php` は、各ページのデータ構造を定義している。
**メタオブジェクト・メタフィールドの設計は、このファイルを基に行う。**

手順:

1. `value/*.php` を読み、各フィールドのデータ型と構造を特定する
2. Shopify のメタオブジェクト/メタフィールドに変換する（型対応表は 10.1 参照）
3. `metaobjects.json` / `metafields.json` にデータ定義を作成する
4. `entries.json` / `blogs.json` に **PHP版のダミーデータと同じ値** でテストデータを作成する

**デフォルトデータは PHP版の value/\*.php の値をそのまま使う。**
schema の default 値や、seed のエントリーデータは PHP版のダミーデータと一致させること。

#### 2.3.3 画像のデフォルト設定

テストデータで使う画像は **1つの代表画像ファイル名を決めて統一する**。

```
代表画像例: pic-main_01.webp
```

- schema の default で画像を指定する場合 → テーマエディタから設定
- entries.json / blogs.json の画像フィールド → ファイル名だけ指定（`"image": "pic-main_01.webp"`）
- seed スクリプトが Files API でファイル名 → GID を自動解決する
- 画像なし時のプレースホルダー → `{案件名}-noimage` スニペット（`assets/common-noimage.webp`）

#### 2.3.4 データの持たせ方

| データの性質                            | 方法                                                 | 例                                    |
| --------------------------------------- | ---------------------------------------------------- | ------------------------------------- |
| ページ全体の固定項目（1つしかないもの） | section.settings                                     | MV画像、ページタイトル                |
| 複数追加・並べ替えが必要な同種の項目    | blocks                                               | スライド画像、ピックアップ記事        |
| 商品/記事ごとに出し分けるコンテンツ     | メタオブジェクト + メタフィールド                    | 商品LP、詳細情報、journal記事ブロック |
| 異なる種類のセクションの混在            | list.mixed_reference                                 | LP の9セクション                      |
| テーブル等の構造データ                  | multi_line_text_field（`項目名\|項目値` 改行区切り） | 素材表、サイズガイド                  |
| 列挙値（カラー等）                      | single_line_text_field + choices バリデーション      | data-color 値                         |

**リッチテキスト（WYSIWYG / rich_text_field）の使用制限:**

- schema や メタオブジェクトでは **基本 NG**
- 使っていいのは **news（ブログ記事の body）** と **privacy系（利用規約等）固定ページ** のみ
- 理由: WYSIWYG は `<p>` タグ等を自動挿入するため、既存のCSS構造（`p.p-text > span`）と競合する

**メタオブジェクトの設計ルール:**

- 1セクション = 1メタオブジェクト定義（不要なフィールドが管理画面に表示されないようにする）
- 選択肢は choices バリデーション（管理画面でドロップダウン表示）
- テーマエディタのブロックは**テンプレート共通**。ページ/商品ごとに違うデータはメタオブジェクトで

#### 2.3.5 Liquid の落とし穴（実装時の注意）

| 問題                                     | 対応                                                                          |
| ---------------------------------------- | ----------------------------------------------------------------------------- |
| 改行で split したい                      | `split: "\n"` は動かない。`newline_to_br \| split: '<br />'` を使う           |
| メタオブジェクトの type を取得したい     | `.type` は空。**`.system.type`** を使う                                       |
| メタオブジェクトの file_reference を取得 | `.value` が必要。`item.image.value \| default: item.image`                    |
| Splide の JSON 設定                      | `data-splide='...'` **シングルクォート + 直書き**。Liquid 変数経由だと壊れる  |
| `data-color` の配置場所                  | `b-sticky` ではなく **`<section>` タグ**に配置。JS がセクション単位で認識する |
| 画像なし時のフォールバック               | `{%- if image -%}{案件名}-image{%- else -%}{案件名}-noimage{%- endif -%}`     |
| render タグのパラメータにフィルター      | `\| append:` 等はパラメータ内で使えない。事前に `assign` する                 |

#### 2.3.6 section.settings の default 値

section.settings の default 値は **PHP版の value/\*.php の値と一致させる**。
テーマエディタで何も設定しなくても、PHP版と同じダミーデータが表示される状態にする。

```json
{
  "type": "text",
  "id": "title",
  "label": "タイトル（英語）",
  "default": "News"
},
{
  "type": "text",
  "id": "subtitle",
  "label": "タイトル（日本語）",
  "default": "お知らせ"
}
```

**section.settings の並び順** はテーマエディタでの表示順になる。重要な設定を先に配置する。

**全セクションに data_color 設定を入れる:**

```json
{
  "type": "select",
  "id": "data_color",
  "label": "カラー",
  "options": [
    { "value": "", "label": "デフォルト" },
    { "value": "color1", "label": "color1 ({_variable.scss のカラー名})" },
    { "value": "color2", "label": "color2 ({カラー名})" },
    { "value": "color3", "label": "color3 ({カラー名})" },
    { "value": "color4", "label": "color4 ({カラー名})" }
  ],
  "default": ""
}
```

#### 2.3.7 PHP関数 → Liquid snippet 対応表（クイックリファレンス）

| PHP                                       | Liquid                                                                                |
| ----------------------------------------- | ------------------------------------------------------------------------------------- |
| `setHtmlBgImage($img, 'p-image')`         | `{% render '{案件名}-image', src: img %}`                                             |
| `setHtmlBgImage($img, 'p-image is-full')` | `{% render '{案件名}-image', src: img, class: 'is-full' %}`                           |
| `setHtmlTitle($val, 'class', 'h2')`       | `{% render '{案件名}-title', main: val_main, sub: val_sub, tag: 'h2', size: 'sec' %}` |
| `setHtmlText($val, 'p-text')`             | `{% render '{案件名}-text', body: val %}`                                             |
| `setHtmlSvg('icon-name')`                 | `{% render '{案件名}-icon', path: 'icon-name' %}`                                     |
| `setHtmlLink($val, 'p-button')`           | `{% render '{案件名}-link', url: val_url, label: val_label %}`                        |
| `setHtmlPagerArr()`                       | `p-pager__arr` 構造を直接書く（snippet なし）                                         |
| `setHtmlPagerNum()`                       | `{% render '{案件名}-pagination', paginate: paginate %}`                              |
| `setHtmlForm()`                           | Shopify `{% form 'contact' %}` + `dl > dt + dd` 構造                                  |
| 画像なし時                                | `{% render '{案件名}-noimage' %}`                                                     |

※ スニペット名は `{案件名}-*` の形式で統一する。`d-*` や `furfolk-*` 等、リポジトリの命名規則に合わせること。

#### 2.3.7.1 PHP関数の実際のHTML出力（絶対に構造を変えない）

PHP の各ヘルパー関数が出力する HTML を正確に把握すること。
**`<div>` でラップしたり `<span>` を省略したりしてはいけない。**

```
setHtmlText($val, 'p-text')
→ <p class="p-text"><span>テキスト</span></p>

setHtmlText($val, 'p-lead')
→ <p class="p-lead"><span>テキスト</span></p>

setHtmlText($val, 'p-lead is-size__large js-sa__op')
→ <p class="p-lead is-size__large js-sa__op"><span>テキスト</span></p>

setHtmlText($val, 'p-message')
→ <p class="p-message"><span>テキスト</span></p>

setHtmlText($val, 'p-date')
→ <p class="p-date"><span>テキスト</span></p>

setHtmlText($val, 'p-price')
→ <p class="p-price"><span>テキスト</span></p>

setHtmlText($val, 'p-caption')
→ <p class="p-caption"><span>テキスト</span></p>
```

**よくある間違い:**

```html
<!-- NG: div でラップ -->
<div class="p-lead"><p>テキスト</p></div>

<!-- NG: span を省略 -->
<p class="p-text">テキスト</p>

<!-- OK: PHP版と同じ -->
<p class="p-lead"><span>テキスト</span></p>
```

#### 2.3.7.2 フォーム要素のラッパー（renderInputField）

PHP の `renderInputField()` は各入力要素をラッパー div で囲む。
**ラッパーを省略するとスタイルが当たらない。**

| input type                | ラッパー                   | HTML                                                                                               |
| ------------------------- | -------------------------- | -------------------------------------------------------------------------------------------------- |
| text / email / tel / date | `<div class="p-input">`    | `<div class="p-input"><input type="text" ...></div>`                                               |
| textarea                  | `<div class="p-textarea">` | `<div class="p-textarea"><textarea ...></textarea></div>`                                          |
| select                    | `<div class="p-select">`   | `<div class="p-select"><span class="arrow"></span><select ...></select></div>`                     |
| radio                     | `<div class="p-radio">`    | `<div class="p-radio"><span><label><input type="radio"><span>値</span></label></span></div>`       |
| checkbox                  | `<div class="p-checkbox">` | `<div class="p-checkbox"><span><label><input type="checkbox"><span>値</span></label></span></div>` |

**select は `<span class="arrow"></span>` が必須**（CSS でカスタム矢印を表示するため）。

#### 2.3.7.3 セクション構造のパターン

PHP版のページは以下のパターンで構成されている。Liquid でも同じ構造を維持すること。

**sticky 構造:**

```html
<div class="b-sticky {ページ固有class}__sticky">
  <div class="b-sticky__bg ..."><!-- ロゴ等 --></div>
  <div class="b-sticky__main ...">
    <div class="b-sticky__main__wrap ...">
      <!-- セクション群 -->
    </div>
  </div>
</div>
```

**セクション構造:**

```html
<section id="セクションID" class="section {ページ固有class}__{セクション名}" data-color="...">
  <div class="section__wrap {ページ固有class}__{セクション名}__wrap">
    <div class="section__inner {ページ固有class}__{セクション名}__inner">
      <!-- コンテンツ -->
    </div>
  </div>
</section>
```

**重要:**

- `data-color` は `<section>` タグに配置（`b-sticky` の div ではない）
- class 名は `ページ固有class__セクション名__要素` の形式を守る
- `is-narrow` の有無は PHP版に合わせる（勝手に追加/削除しない）

#### 2.3.8 変換完了チェックリスト（各セクションごとに確認）

変換したセクションについて、以下を全て確認してから完了とする:

- [ ] PHP版のHTML出力と class 名が完全に一致しているか
- [ ] `<span>` タグが抜けていないか
- [ ] `<div class="p-input">` 等のラッパー要素が抜けていないか
- [ ] `data-color` が `<section>` タグに配置されているか（`b-sticky` ではなく）
- [ ] `data-splide` がシングルクォートで直書きされているか
- [ ] 画像が {案件名}-image スニペット経由で出力されているか
- [ ] 画像なし時に {案件名}-noimage が表示されるか
- [ ] section.settings の default 値が PHP版の value と一致しているか
- [ ] メタオブジェクトのフィールド参照に `.value` が付いているか
- [ ] 改行 split が `newline_to_br | split: '<br />'` を使っているか

### 2.3.1 PHP → Liquid 変換パターン

| PHP                                        | Liquid                                                                         |
| ------------------------------------------ | ------------------------------------------------------------------------------ |
| `<?php echo $var ?>`                       | `{{ var }}`                                                                    |
| `<?php if (...): ?>`                       | `{%- if ... -%}`                                                               |
| `<?php foreach ($arr as $item): ?>`        | `{%- for item in arr -%}`                                                      |
| `include($path)`                           | `{% render 'snippet-name' %}`                                                  |
| `$_GET['param']`                           | `{{ request.params.param }}`                                                   |
| PHP 配列 → JSON                            | `data-splide='{"type":"loop",...}'` 直書き                                     |
| `setHtmlSvg('icon-name')`                  | `{% render 'd-icon', path: 'icon-name' %}`                                     |
| `setHtmlBgImage($img, 'p-image')`          | `{% render 'd-image', src: img, alt: '' %}`                                    |
| `setHtmlTitle($val, 'p-title__sec', 'h2')` | `{% render 'd-title', main: val.main, sub: val.sub, tag: 'h2', size: 'sec' %}` |
| `setHtmlText($val, 'p-text')`              | `{% render 'd-text', body: val %}`                                             |
| `setHtmlLink($val, 'p-button')`            | `{% render 'd-link', url: val.url, label: val.title %}`                        |
| `setHtmlNav($menu, 'header', $class)`      | `{% render 'd-nav', menu_handle: 'main-menu', class: 'l-header__nav' %}`       |

### 2.4 ファイル配置の対応

| PHP の構造                            | Shopify の構造                                 |
| ------------------------------------- | ---------------------------------------------- |
| `_src/_inc/_l-page.php`               | `layout/theme.liquid`                          |
| `_src/_inc/_l-header.php`             | `sections/header.liquid`                       |
| `_src/_inc/_l-footer.php`             | `sections/footer.liquid`                       |
| `_src/_inc/function/blocks/nav.php`   | `snippets/d-nav.liquid`                        |
| `_src/_inc/function/parts/logo.php`   | header.liquid 内に直接記述                     |
| `_src/_inc/page/top.php`              | `sections/d-top-*.liquid`（セクション分割）    |
| `_src/_inc/page/products_single.php`  | `sections/d-product-detail.liquid`             |
| `_src/_inc/page/products_archive.php` | `sections/d-collection-grid.liquid`            |
| `_src/_inc/page/faq.php`              | `sections/d-faq.liquid`                        |
| `_src/_inc/page/terms.php` 等         | `sections/d-static-page.liquid`                |
| `_src/_inc/page/contact.php`          | `sections/d-contact-form.liquid`               |
| `_src/_inc/page/news_archive.php`     | `sections/d-blog-archive.liquid`               |
| `_src/_inc/page/news_single.php`      | `sections/d-article.liquid`                    |
| `_src/_inc/value/*.php`               | テーマ設定 / メタフィールド / メタオブジェクト |
| `_src/_inc/meta/meta.php`             | `layout/theme.liquid` の `<head>` 内           |
| `_src/scss/`                          | `assets/{案件名}.css`（Viteビルド）            |
| `_src/js/`                            | `assets/{案件名}.js`（Viteビルド）             |
| `_src/icon/*.svg`                     | `assets/sprite.svg`（svg-sprite ビルド）       |

### 2.5 shopify.theme.toml

Dawn 取得後またはリポジトリ初期化時に、対象ストアを設定する。
**ファイルが存在しない場合はプロジェクトルートに新規作成すること。**

```toml
[environments.development]
store = "your-store.myshopify.com"
```

これにより `shopify theme dev` / `shopify theme push` で毎回 `--store` を指定する必要がなくなる。
ユーザーに対象ストアのURLを確認してから作成する。

### 2.6 PHP function → Liquid snippet 対応表

PHPの `_inc/function/` 配下のヘルパー関数は、Liquidのスニペットに変換する。
**parts/（単一要素）→ snippets に1:1対応、blocks/（複合要素）→ sections 内に組み込み** が基本。

#### parts/ → snippets/（単一UIパーツ）

| PHP (`_inc/function/parts/`)        | Liquid snippet                       | 備考                           |
| ----------------------------------- | ------------------------------------ | ------------------------------ |
| `svg.php` (`setHtmlSvg`)            | `d-icon.liquid`                      | SVG sprite 参照                |
| `image.php` (`setHtmlBgImage`)      | `d-image.liquid`                     | lazy load + SP出し分け         |
| `title.php` (`setHtmlTitle`)        | `d-title.liquid`                     | main/sub 2段、size指定         |
| `text.php` (`setHtmlText`)          | `d-text.liquid`                      | newline_to_br 対応             |
| `link.php` (`setHtmlLink`)          | `d-link.liquid`                      | 外部リンク自動判定             |
| `logo.php` (`setHtmlLogo`)          | header.liquid 内に直接記述           | SVG sprite or img タグ         |
| `pankuzu.php` (`setHtmlPankuzu`)    | `d-breadcrumb.liquid`                | template 変数で自動判定        |
| `pager.php` (`setHtmlPagerNum`)     | `d-pagination.liquid`                | Shopify paginate 使用          |
| `sns.php` (`setHtmlSns`)            | `d-sns.liquid`                       | テーマ設定から取得             |
| `taxonomy.php` (`setHtmlTaxonomy`)  | タグ表示は Liquid 直書き             | `article.tags` 等              |
| `share.php` (`setHtmlShare`)        | Dawn の `share-button.liquid` 流用可 |                                |
| `table.php` (`setHtmlTable`)        | 必要に応じて作成                     |                                |
| `tel.php`                           | 不要（Shopify はリンクで対応）       |                                |
| `search.php`                        | Dawn の `main-search.liquid` で対応  |                                |
| `map.php`                           | Google Maps は JS で直接埋め込み     |                                |
| `youtube.php` / `movie.php`         | 必要に応じてスニペット作成           |                                |
| `dl.php` / `li.php` / `info.php` 等 | セクション内に直接記述               | 汎用性低いためスニペット化不要 |

#### blocks/ → sections/（複合ブロック）

| PHP (`_inc/function/blocks/`)    | Shopify での対応                                          |
| -------------------------------- | --------------------------------------------------------- |
| `nav.php` (`setHtmlNav`)         | `snippets/d-nav.liquid`（例外的にスニペット化）           |
| `form.php` (`setHtmlForm`)       | `sections/d-contact-form.liquid`（Shopify form タグ使用） |
| `faq.php`                        | `sections/d-faq.liquid` のブロック or メタオブジェクト    |
| `body.php` (`setHtmlBody`)       | `page.content` で対応（Shopify リッチテキストエディタ）   |
| `gallery.php`                    | Splide セクション内に組み込み                             |
| `mv.php`                         | `sections/d-top-mv.liquid`                                |
| `box.php`                        | セクション内に直接記述                                    |
| `wysiwyg.php`                    | `page.content` or メタフィールド（rich_text）             |
| `history.php` / `recruit.php` 等 | 案件固有。必要に応じてセクション作成                      |

### 2.7 セクション設計の原則

- 全セクションの schema に `color_scheme` 設定を入れる
- 最外側の HTML 要素に `color-{{ section.settings.color_scheme }} gradient` クラスを付与
- ヘッダー/フッターは snippets ではなく **sections** に置き、section group で読み込む
- メニュー選択は schema の `link_list` 型で、テーマエディタから変更可能にする
- トップページは機能単位でセクション分割（MV / Story / Products / Journal / News / Instagram 等）

### 2.8 PHP → Shopify 変換時の判断基準

Claudeが変換作業中に迷いやすいポイントと、その判断ルール。

#### 無視してよいPHPコード

| PHPの仕組み                                  | 判断         | 理由                                                                                                                     |
| -------------------------------------------- | ------------ | ------------------------------------------------------------------------------------------------------------------------ |
| `$wpflg` 分岐（WordPress連携）               | **全て無視** | Shopify移行で不要                                                                                                        |
| `setAcf*()` 関数（ACF設定）                  | **全て無視** | Shopifyにはメタフィールド/メタオブジェクトで対応するが、テーマファイルからは定義不可。管理画面設定の指示書として別途出力 |
| `$confirmFlg`（メール送信処理）              | **無視**     | Shopify の Liquid ではサーバーサイド処理不可                                                                             |
| `require_once`, `include` のパス解決ロジック | **無視**     | Liquid の `render` に置き換え済み                                                                                        |
| `$_GET`, `$_POST`, `$_SERVER`                | **無視**     | Shopify では `request` オブジェクトまたは JS で対応                                                                      |

#### デバイス判定（`is_mobile()` / `is_tablet()`）

PHP のサーバーサイドデバイス判定は Liquid にない。**CSSメディアクエリに変換する。**

- 変換時に `is_mobile()` / `is_tablet()` を使っている箇所を**全て検出・報告**すること
- 報告フォーマット: ファイル名、行番号、何をデバイス分岐しているか（画像の出し分け、表示/非表示 等）
- 画像の出し分け → `d-image.liquid` の `src_sp` パラメータ + `show_sp` / `show_pctb` クラスで対応
- 表示/非表示 → CSS の `show_sp` / `show_pctb` / `show_pc` クラスで対応
- **Liquid側で分岐せず、全デバイス分のHTMLを出力してCSSで制御する**

#### フォーム確認画面

PHPの確認→送信の2ステップフォームは、Shopifyでは以下で対応:

- Shopify 標準の `{% form 'contact' %}` で送信
- 確認画面は **JavaScript で実装**（フォーム送信前にプレビュー表示）
- `contact_common.php` の確認画面ロジックはJS側の実装指示として出力

#### ページ値（`_inc/value/page/*.php`）の変換先

PHPの `defaultPageValue()` / `$this_page_value` で管理していた値は、**基本的にセクション設定（schema settings）に変換する。**

| PHPの値の種類                        | Shopifyでの変換先                                          |
| ------------------------------------ | ---------------------------------------------------------- |
| ページタイトル（main/sub）           | セクション schema の `settings` → `default` に設定         |
| セクションのテキスト・リード文       | セクション schema の `settings` → `default` に設定         |
| カラー指定                           | セクション schema の `settings`（select型）→ `default`     |
| リスト（FAQ項目、スライド等）        | セクション schema の `presets` → `blocks` に初期値定義     |
| 画像パス                             | 設定不可。管理画面で設定（`image_picker` に default なし） |
| URL / リンク先                       | ラベルのみ default、URL は管理画面で設定                   |
| 商品固有データ（価格、オプション等） | Shopify の product オブジェクトから取得                    |
| 商品ごとに異なるコンテンツ           | メタオブジェクト + リスト型メタフィールド                  |
| サイト共通値（`_common.php`）        | `config/settings_data.json` に設定                         |

#### JSライブラリ（Splide, Three.js 等）

- Vite ビルドに含める（CDN に切り替えない）
- `_src/main.js` から import されていればそのまま `{案件名}.js` に含まれる
- Splide の `data-splide` JSON設定はPHPの `json_encode()` から直書きJSONに変換

#### ブログ記事のコンテンツ方式（WYSIWYG vs メタオブジェクト）

案件によってブログの種類ごとにコンテンツ管理方式が異なる場合がある。
**ブログごとに方式を判断し、ユーザーに確認すること。**

| 方式                                                               | 適するケース                              | 管理画面での入力                     |
| ------------------------------------------------------------------ | ----------------------------------------- | ------------------------------------ |
| **WYSIWYG**（`article.content`）                                   | 短い告知、テキスト中心の記事              | リッチテキストエディタで入力         |
| **メタオブジェクト**（`article.metafields.custom.article_blocks`） | 画像+テキストの交互配置、凝ったレイアウト | ブロックを追加して各フィールドに入力 |

**判断基準：**

- ブログの記事にカスタムクラス（`p-lead`, `p-text` 等）を使ったレイアウトが必要 → メタオブジェクト
- 素のHTML（h2, p, img, ul 等）で十分 → WYSIWYG
- クライアントがHTMLを書けない + 凝った構成が必要 → メタオブジェクト

**Liquid 側は両方に対応する設計にする：**

```liquid
{%- assign blocks = article.metafields.custom.article_blocks.value -%}
{%- if blocks != blank -%}
  {%- comment -%} メタオブジェクト方式（ブロック管理） {%- endcomment -%}
  {%- for block in blocks -%}
    {%- assign block_type = block.block_type.value | default: block.block_type -%}
    {%- case block_type -%}
      {%- when 'heading' -%}
        {% render 'd-title', main: block.title, tag: 'h2', size: 'article' %}
      {%- when 'text' -%}
        <div class="p-text">{{ block.description }}</div>
      {%- when 'image' -%}
        {% render 'd-image', src: block.image, alt: block.caption %}
        {%- if block.caption != blank -%}
          <p class="p-caption">{{ block.caption }}</p>
        {%- endif -%}
      {%- when 'link' -%}
        {% render 'd-link', url: block.url, label: block.url_label %}
      {%- when 'quote' -%}
        <blockquote class="p-quote"><p>{{ block.description }}</p></blockquote>
    {%- endcase -%}
  {%- endfor -%}
{%- else -%}
  {%- comment -%} WYSIWYG フォールバック {%- endcomment -%}
  <div class="b-body">{{ article.content }}</div>
{%- endif -%}
```

**WYSIWYG 方式のCSS対応：**
`b-body` 内の素のHTMLタグにスタイルを当てる。既存SCSSに `b-body` のスタイルがあればそのまま使える。
なければ以下のような基本スタイルを追加する：

```scss
.b-body {
  h2 {
    /* p-title__article 相当 */
  }
  h3 {
    /* p-title__box 相当 */
  }
  p {
    /* p-text 相当 */
  }
  img {
    width: 100%;
    height: auto;
  }
  blockquote {
    /* p-quote 相当 */
  }
  ul,
  ol {
    /* リスト */
  }
  table {
    /* テーブル */
  }
}
```

**メタオブジェクト定義（seed スクリプトで作成）：**

type: `article_content` / フィールド: `block_type`, `title`, `description`(rich_text), `image`, `caption`, `url`, `url_label`

記事のメタフィールド: `custom.article_blocks`（list.metaobject_reference → article_content）

#### FAQのコンテンツ管理（メタオブジェクト方式）

FAQはページごとに項目が異なるため、**テーマエディタのブロックではなくメタオブジェクトで管理する。**

テーマエディタのブロックはテンプレート共通のため、ページごとに違うFAQ項目を設定できない。
商品詳細ページ内のFAQも同様にメタオブジェクトで管理する。

**メタオブジェクト定義:**

type: `faq_item` / フィールド:

- `question`（単一行テキスト、必須）— 質問
- `answer`（リッチテキスト）— 回答

**メタフィールド定義:**

| 対象                      | namespace.key      | 型                                   |
| ------------------------- | ------------------ | ------------------------------------ |
| ページ（FAQページ用）     | `custom.faq_items` | list.metaobject_reference → faq_item |
| 商品（商品ページ内FAQ用） | `custom.faq_items` | list.metaobject_reference → faq_item |

**Liquid 側の実装:**

```liquid
{%- comment -%} FAQ ページの場合 {%- endcomment -%}
{%- assign faq_items = page.metafields.custom.faq_items.value -%}

{%- comment -%} 商品ページ内の場合 {%- endcomment -%}
{%- assign faq_items = product.metafields.custom.faq_items.value -%}

{%- if faq_items != blank -%}
  <div class="b-faq">
    {%- for item in faq_items -%}
      {%- assign q = item.question.value | default: item.question -%}
      {%- assign a = item.answer.value | default: item.answer -%}
      <dl class="b-faq__dl js-accordion">
        <dt class="b-faq__dt js-accordion__head">
          {%- render 'd-text', body: q -%}
        </dt>
        <dd class="b-faq__dd js-accordion__body">
          {{ a }}
        </dd>
      </dl>
    {%- endfor -%}
  </div>
{%- endif -%}
```

**管理画面での操作:**

- FAQページ → ページ編集画面下部の「FAQ項目」でメタオブジェクトを追加
- 商品ページ → 商品編集画面下部の「FAQ項目」でメタオブジェクトを追加
- 同じ faq_item メタオブジェクトを複数ページ/商品で共有することも可能

#### メタオブジェクト/メタフィールドのテキスト型は `rich_text_field` を使わない

`rich_text_field` は Shopify が自動的に `<p>`, `<ul>`, `<h2>` 等のHTMLタグを含む文字列を返す。
`d-text` スニペットは `<p class="p-text"><span>...</span></p>` を出力するため、
richtext の値を渡すと **`<p>` の中に `<p>` がネストして壊れる。**

**基本方針: `rich_text_field` は使わず、`single_line_text_field` または `multi_line_text_field` を使う。**

| フィールド型             | 用途                             | Liquid出力                                                 |
| ------------------------ | -------------------------------- | ---------------------------------------------------------- |
| `single_line_text_field` | 1行テキスト（タイトル、質問等）  | `{% render 'd-text', body: value %}`                       |
| `multi_line_text_field`  | 複数行テキスト（回答、説明文等） | `{% render 'd-text', body: value %}`（newline_to_br 対応） |
| `rich_text_field`        | **基本使わない**                 | 使う場合は `{{ value }}` で直接出力（d-text を通さない）   |

FAQ の場合:

- `question`（single_line_text_field）→ `d-text` で出力
- `answer`（multi_line_text_field）→ `d-text` で出力（改行は newline_to_br で `<br>` に変換）

`rich_text_field` を使うのは `page.content` / `article.content`（WYSIWYG）のみ。
メタオブジェクトのフィールドには使わない。

#### セクション schema のテキスト設定も `richtext` を使わない

schema の settings / blocks で `"type": "richtext"` は使わない。
代わりに `"type": "textarea"`（複数行）または `"type": "text"`（1行）を使う。

| schema type       | 用途                                   | 備考                                               |
| ----------------- | -------------------------------------- | -------------------------------------------------- |
| `text`            | 1行テキスト（タイトル、ラベル等）      |                                                    |
| `textarea`        | 複数行テキスト（本文、回答、説明文等） | Liquid 内で `newline_to_br` で改行を `<br>` に変換 |
| `richtext`        | **使わない**                           | HTML が混入し、d-text のネスト問題を引き起こす     |
| `inline_richtext` | **使わない**                           | 同上                                               |

理由:

- `richtext` は `<p>`, `<b>`, `<em>` 等のHTMLタグ付きの文字列を返す
- d-text スニペットの `<p class="p-text">` と二重ネストして壊れる
- クライアントが太字等の装飾を入れるとHTML構造が予測不能になる
- `textarea` + `newline_to_br` で十分な表現力がある

#### メタオブジェクトで管理すべきコンテンツの判断基準

| 条件                              | 管理方法                                       |
| --------------------------------- | ---------------------------------------------- |
| テンプレート共通の固定コンテンツ  | セクション schema の settings / blocks         |
| ページ/商品ごとに異なるコンテンツ | **メタオブジェクト**                           |
| クライアントがHTMLを書けない      | **メタオブジェクト**（フィールド入力のみ）     |
| 並べ替えが必要なリスト            | **メタオブジェクト**（リスト型メタフィールド） |
| 単純な文章のみ                    | WYSIWYG（`page.content` / `article.content`）  |

具体的にメタオブジェクトで管理すべきもの:

- **FAQ項目** — ページ/商品ごとに異なる（`faq_item`）
- **商品コンテンツブロック** — 商品ごとに異なるレイアウト（`product_content`）
- **Journal記事ブロック** — 記事ごとに異なる構成（`article_content`）

---

## 3. 必須スニペット（毎回生成すること）

以下のスニペットは**どの案件でも必ず生成**する。
案件のプレフィックス（例: `d-`）を付けて、Dawnのスニペットと競合しないようにする。

### 3.1 d-icon.liquid

SVG sprite からアイコンを表示。
パラメータ: `path`, `addclass`, `color`, `style`

```liquid
{% render 'd-icon', path: 'icon-arrow', addclass: 'my-class', color: '#000' %}
```

sprite.svg は `spriteConfig.json` の出力先を `assets/` に設定してビルドする。

### 3.2 d-image.liquid

背景画像（lazy load 対応）。SP画像の出し分けに対応。
パラメータ: `src`, `alt`, `class`, `src_sp`, `parallax`, `attributes`

```liquid
{% render 'd-image', src: product.featured_image, alt: product.title, class: 'is-main' %}
```

#### lazy load のクラス名・属性は JS と完全一致させること（重要）

PHP版の `setHtmlBgImage` の出力:

```html
<div class="p-image js-lazyImage {class}">
  <span class="js-lazyImage__bgi" data-src="画像URL">alt</span>
</div>
```

JS（`_src/js/index.js`）は `.js-lazyImage__bgi[data-src]` セレクタで要素を検索し、
IntersectionObserver で `data-src` の値を `background-image` にセットする。

**やりがちなミス:**

- `js-lazy_bgi` のようにクラス名を勝手に略す → JS が見つけられず画像が表示されない
- `data-bgi` のように属性名を変える → 同上
- `js-lazyImage` クラスを div に付け忘れる → `is-lazyLoad` 状態クラスが付かない

**正しいクラス名・属性名（必ず守る）:**

| 要素          | クラス / 属性                      |
| ------------- | ---------------------------------- |
| 外側 `<div>`  | `p-image js-lazyImage {追加class}` |
| 内側 `<span>` | `js-lazyImage__bgi`                |
| 画像URL属性   | `data-src`                         |

スニペット（d-image.liquid）がこの出力になっていれば、
sections 側は `{% render 'd-image' %}` を呼ぶだけでよい。

### 3.3 d-title.liquid

タイトル（main + sub の2段構成）。
パラメータ: `main`, `sub`, `tag`(default: h2), `size`(default: section), `addclass`, `sa`

```liquid
{% render 'd-title', main: 'Brand Story', sub: 'ブランドストーリー', tag: 'h2', size: 'sec' %}
```

size の種類: `page`, `sec`(section), `sec3`, `box`, `article`, `news`, `itemThumb`, `item`

### 3.4 d-text.liquid

テキスト（`newline_to_br` 対応）。
パラメータ: `body`, `addclass`

```liquid
{% render 'd-text', body: 'テキスト内容', addclass: 'is-large' %}
```

### 3.5 d-link.liquid

ボタンリンク。外部リンクの自動判定（target, icon 切り替え）付き。
パラメータ: `url`, `label`, `class`, `addclass`, `attributes`

```liquid
{% render 'd-link', url: '/products', label: '詳しく見る' %}
```

### 3.6 d-price.liquid

価格表示。セール判定（compare_at_price）対応。
パラメータ: `product` または `price` + `compare_at_price`, `class`

```liquid
{% render 'd-price', product: product, class: 'is-small' %}
```

### 3.7 d-nav.liquid

ナビゲーション。Shopify linklist から生成。
`|` 区切りで title/subtitle を分割表示。子メニュー対応。
パラメータ: `menu_handle`, `class`

```liquid
{% render 'd-nav', menu_handle: 'main-menu', class: 'l-header__nav' %}
```

管理画面のメニュータイトル入力規約: `商品一覧|Products`

### 3.8 d-breadcrumb.liquid

パンくずリスト。Shopify のオブジェクトからラベルを自動取得する。
**テンプレートごとの if 分岐を最小限にする。**

**PHP版の出力例:**

```html
<ol class="p-pankuzu">
  <li>
    <a href="/"><span>ホーム</span></a>
  </li>
  <li>
    <a href="/products/"><span>商品一覧</span></a>
  </li>
  <li>
    <div><span>商品名</span></div>
  </li>
</ol>
```

**構造のルール:**

- 外側は `<ol class="p-pankuzu">` のみ。`<nav>` で囲まない
- `<li>` に class は付けない
- リンク要素: `<a href="..."><span>title</span></a>`
- 現在ページ（最後の要素）: `<div><span>title</span></div>`
- `is-current` 等の状態クラスは付けない
- トップページ（`template == 'index'`）では何も出力しない
- ラベルは全て日本語（管理画面で入力したタイトルがそのまま使われる）
- 「ホーム」のみ固定文字列

**実装パターン（テンプレート別 if を最小限にする）:**

```liquid
{%- unless template == 'index' -%}
<ol class="p-pankuzu">
  <li><a href="/"><span>ホーム</span></a></li>
  {%- if collection -%}
    <li><a href="{{ collection.url }}"><span>{{ collection.title }}</span></a></li>
  {%- elsif blog -%}
    <li><a href="{{ blog.url }}"><span>{{ blog.title }}</span></a></li>
  {%- endif -%}
  {%- if template != 'collection' and template != 'blog' -%}
    {%- assign current_title = page.title
      | default: product.title
      | default: article.title
      | default: collection.title
      | default: blog.title -%}
    {%- if current_title != blank -%}
      <li><div><span>{{ current_title }}</span></div></li>
    {%- endif -%}
  {%- endif -%}
</ol>
{%- endunless -%}
```

**ラベルの取得元:**

- ホーム: 固定「ホーム」
- 中間階層: `collection.title` または `blog.title`（Shopify が自動で提供）
- 現在ページ: `page.title` / `product.title` / `article.title` 等（管理画面の日本語タイトル）

テンプレート別の分岐は「中間階層（collection/blog）の有無」のみ。
各タイトルは管理画面で日本語入力すればそのまま日本語で表示される。

### 3.9 d-pagination.liquid

ページネーション。
パラメータ: `paginate`

### 3.10 d-product-card.liquid

商品カードの1アイテム分。
パラメータ: `product`

### 3.11 d-product-list.liquid

商品グリッド（product-card のリスト）。
パラメータ: `products`, `limit`

### 3.12 d-sns.liquid

SNSリンク。テーマ設定（`settings.social_*_link`）から取得。

---

## 4. 不要な Dawn ファイルの削除

Dawn テーマ取得後、上記カスタムセクションに置き換えた Dawn セクションは削除する。

### 4.1 削除対象の判定方法

1. `templates/*.json` 内の全 `"type"` 値を抽出
2. `sections/*-group.json` 内の全 `"type"` 値を抽出
3. `layout/theme.liquid` で参照しているセクション/スニペットを抽出
4. 上記で参照されていない `sections/*.liquid` を削除候補とする

### 4.2 通常削除できるセクション

カスタムセクションで置き換えた場合に不要になるもの:

- `main-product.liquid` → `sections/d-product-detail.liquid`
- `main-collection-banner.liquid` + `main-collection-product-grid.liquid` → `d-collection-grid.liquid`
- `main-page.liquid` → `d-static-page.liquid`
- `main-article.liquid` → `d-article.liquid`
- `main-blog.liquid` → `d-blog-archive.liquid`
- `contact-form.liquid` → `d-contact-form.liquid`
- `image-banner.liquid`, `rich-text.liquid`, `slideshow.liquid`, `video.liquid` 等 → トップページ用カスタムセクション
- `collage.liquid`, `multicolumn.liquid`, `multirow.liquid` → 使わない場合
- `featured-blog.liquid`, `featured-product.liquid` → 使わない場合
- `newsletter.liquid`, `pickup-availability.liquid` → 使わない場合

### 4.3 残すべきセクション

- `header.liquid` / `footer.liquid` — カスタム済みだが section group で必要
- `header-group.json` / `footer-group.json` — section group 定義
- `announcement-bar.liquid` — 必要に応じて
- `main-cart-items.liquid` / `main-cart-footer.liquid` — カート機能
- `main-search.liquid` — 検索機能
- `main-404.liquid` — 404 ページ
- `main-account.liquid`, `main-login.liquid` 等 — 顧客アカウント系
- `main-password-header.liquid` / `main-password-footer.liquid` — パスワードページ
- `related-products.liquid` — 商品ページで使用
- `featured-collection.liquid` — トップページ等で使用する場合
- `email-signup-banner.liquid` — 必要に応じて

### 4.4 不要な CSS/JS の削除

削除したセクションが読み込んでいた CSS（`section-*.css`, `component-*.css`）も不要になるが、他のセクションやスニペットが参照している可能性があるため、`grep` で参照がないことを確認してから削除する。

```bash
# 例: section-featured-blog.css が参照されているか確認
grep -r "section-featured-blog" sections/ snippets/ layout/ templates/
```

### 4.5 不要なスニペットの削除

同様に、削除したセクションからのみ参照されていたスニペットも削除候補。`render` / `include` で検索して参照元がないことを確認する。

---

## 5. ビルド設定の変更（Vite / package.json / spriteConfig）

### 5.1 vite.config.js の変更点

PHP版からの主な変更:

- `outDir`: `_public/assets` → `assets`（Shopify直下）
- `emptyOutDir`: `false`（Dawn の既存アセットを消さない）
- `assetsDir`: `''`（サブディレクトリなし）
- `entryFileNames` / `assetFileNames`: フラットファイル名に変更
- `ViteLiveReload`: PHP監視 → Liquid監視に変更

```js
import { defineConfig } from "vite";
import path from "node:path";
import vue from "@vitejs/plugin-vue";
import ViteLiveReload from "vite-plugin-live-reload";

export default defineConfig(({ mode }) => ({
  publicDir: false,
  plugins: [vue(), ViteLiveReload(["templates/**/*.liquid", "sections/**/*.liquid", "snippets/**/*.liquid"])],
  resolve: {
    alias: {
      "@icon": path.resolve(__dirname, "_src/icon"),
      "@image": path.resolve(__dirname, "_src/image"),
      "@js": path.resolve(__dirname, "_src/js"),
      "@scss": path.resolve(__dirname, "_src/scss"),
    },
  },
  server: {
    host: "0.0.0.0",
    port: 5173,
    strictPort: true,
    watch: {
      ignored: ["**/assets/image/**"],
    },
  },
  build: {
    outDir: "assets",
    emptyOutDir: false,
    assetsDir: "",
    manifest: true,
    rollupOptions: {
      input: "_src/main.js",
      output: {
        entryFileNames: "{案件名}.js",
        assetFileNames: (info) => (info.name?.endsWith(".css") ? "{案件名}.css" : "{案件名}-[name][extname]"),
      },
    },
  },
}));
```

### 5.2 package.json のスクリプト変更

PHP版のスクリプト（php, inc, wpassets, server 等）を削除し、Shopify用に置き換える。

**削除するスクリプト:**

- `php` — PHP テンプレートコピー（不要）
- `inc` — \_inc コピー（不要）
- `wpassets` — WordPress アセットコピー（不要）
- `server` / `server2` — PHP ビルトインサーバー（不要）
- `bs` / `dev:bs` — BrowserSync（shopify theme dev が代替）
- `watch:inc`, `watch:php`, `watch:page`, `watch:file`, `watch:wpassets` — 不要

**追加・変更するスクリプト:**

```json
{
  "scripts": {
    "dev": "run-p vite:dev shopify:dev watch",
    "vite:dev": "vite --mode development",
    "shopify:dev": "shopify theme dev",
    "build": "npm run clean && npm run build:vite && run-s imagemin svgsprite",
    "build:vite": "vite build --mode production",
    "watch": "run-p -c watch:scss watch:svg watch:image",
    "watch:scss": "onchange '_src/scss/**/*.scss' -e '**/*.DS_Store' -- npm run build:vite",
    "watch:svg": "onchange '_src/icon' -e '**/*.DS_Store' -- run-s svgsprite",
    "watch:image": "onchange '_src/image/**/*' -e '**/*.DS_Store' -- node scripts/sharp.mjs --watch {{changed}}",
    "imagemin": "node ./scripts/sharp.mjs",
    "svgsprite": "svg-sprite -C spriteConfig.json _src/icon/*svg",
    "clean": "rimraf assets/{案件名}.{js,css}",
    "deploy": "shopify theme push"
  }
}
```

**変更のポイント:**

- `dev`: PHP サーバー → `shopify theme dev` に置き換え
- `build`: php, inc, file, wpassets のコピー処理を削除
- `clean`: `_public/assets/` → `assets/` に変更
- `deploy`: `shopify theme push` を追加
- `watch`: SCSS / SVG / 画像を監視（Liquid ファイルは shopify theme dev が自動検知）
- `watch:scss`: SCSS 変更時に Vite ビルドを実行して `assets/{案件名}.css` を更新。
  Vite の dev サーバーモードは assets/ に出力しないため、onchange + build:vite で対応する。
  shopify theme dev が assets/ の変更を検知してストアに反映する

### 5.3 spriteConfig.json の変更

出力先を `_public/assets/image/icon` → `assets` に変更:

```json
{
  "dest": "assets",
  "mode": {
    "symbol": {
      "dest": ".",
      "sprite": "sprite.svg"
    }
  }
}
```

### 5.4 画像最適化の出力先変更

PHP版では `_public/assets/image/` に出力していたが、Shopify版では `assets/` に直接出力する。
**`_public/` への出力は全て不要。**

`scripts/sharp.mjs` の出力先を変更:

- 変更前: `_public/assets/image/`
- 変更後: `assets/`

Shopify の `assets/` はフラット構造のため、サブディレクトリを使えない。
画像ファイル名にプレフィックスを付けて管理する（例: `pic-xxx.webp`）。

`watch:image` の出力先も同様に `assets/` にする。

### 5.5 画像ビルド（scripts/sharp.mjs）の出力先変更

PHP版では `_public/assets/image/` に出力していたが、Shopify では `assets/` に直接出力する。

```javascript
// scripts/sharp.mjs
// 変更前
const OUTPUT_DIR = "_public/assets/image";
// 変更後
const OUTPUT_DIR = "assets";
```

**注意:** Shopify の `assets/` はフラット構造（サブディレクトリ不可）。
`_src/image/common/noimage.jpg` → `assets/noimage.webp` のようにフラットに出力される。
サブフォルダ名がファイル名のプレフィクスとして必要な場合はスクリプトの調整が必要。

#### npm scripts

```bash
npm run imagemin     # 全画像を一括変換（scripts/sharp.mjs）
npm run watch:image  # 画像ファイルの変更を監視して自動変換
npm run build        # Vite + imagemin + SVG sprite を一括実行
```

### 5.6 .gitignore の追加項目

Vite ビルド成果物をgit管理対象外にする:

```
assets/{案件名}.js
assets/{案件名}.css
assets/.vite/
```

---

## 6. .shopifyignore（必須）

`shopify theme dev` / `shopify theme push` は `.gitignore` を参照しない。
以下を `.shopifyignore` に記載:

```
*.tmp*
_src/
_public/
_wp/
node_modules/
_bk/
*.log
.DS_Store
docker-compose.yml
scripts/sharp.mjs
spriteConfig.json
postcss.config.cjs
vite.config.js
package.json
package-lock.json
readme.md
index.html
indexValue/
memo.php
.env
.git/
.claude/
docs/
```

### 6.1 Git 容量制限対策（Shopify GitHub 連携）

Shopify の GitHub 連携はリポジトリサイズに制限がある（50MB）。
`.git/` の履歴にある大きいファイルもサイズに含まれる。

**容量が超えそうな場合の対処:**

1. **`_src/image/` のソース画像を `.gitignore` に追加**
   ソース画像は Shopify の Files で管理する。git には含めない。

   ```
   _src/image/_dummy/
   _src/image/pic/
   ```

2. **`assets/` の大きい画像を `.gitignore` に追加**
   ビルド出力の画像（png/jpg）が大きい場合。

   ```
   assets/*.png
   assets/*.jpg
   ```

   ※ webp, css, js, svg は残す

3. **git 履歴から大きいファイルを除去**
   既にコミット済みの大きいファイルは `.gitignore` に追加しても `.git/` に残る。

   ```bash
   pip3 install --break-system-packages --user git-filter-repo
   export PATH="$HOME/Library/Python/3.12/bin:$PATH"
   git filter-repo --path _src/image/_dummy --invert-paths --force
   git filter-repo --path-glob 'assets/*.webp' --invert-paths --force
   git filter-repo --path-glob 'assets/*.webp' --invert-paths --force
   ```

   ※ `git filter-repo` は remote を削除するので、再設定が必要:

   ```bash
   git remote add origin https://github.com/xxx/xxx.git
   git push origin ブランチ名 --force
   ```

4. **サイズ確認**
   ```bash
   du -sh .git/           # git 履歴サイズ
   du -sh assets/         # assets サイズ
   du -sh _src/image/     # ソース画像サイズ
   ```

**テーマファイルだけなら 5-10MB 程度。** 画像を git から除外すれば 50MB 以下に収まる。

**デプロイは `shopify theme push` で行い、GitHub 連携はコード管理のみに使う運用も可。**

---

## 7. 管理画面設定チェックリスト

テーマファイル完成後、Shopify 管理画面で以下を設定:

- [ ] メニュー作成（main-menu, sub-menu, footer-sub-menu, product-categories）
- [ ] コレクション作成（案件に応じたカテゴリ）
- [ ] ブログ作成（News, Journal 等）
- [ ] 固定ページ作成 + テンプレート割り当て（FAQ, Contact, Terms, Privacy, Legal 等）
- [ ] メタオブジェクト定義（商品コンテンツブロック等）
- [ ] メタフィールド定義（商品: name_kana, content_blocks / コレクション: title_sub 等）
- [ ] テーマエディタ: トップページ各セクション設定
- [ ] テーマ設定: ロゴ, ファビコン, SNSリンク
- [ ] テーマをストアに push (`shopify theme push`)
- [ ] 固定ページのテンプレート割り当て（push 後でないとテンプレート選択肢が表示されない）

---

## 8. 管理画面の入力手間を省く方法

### 8.1 schema の default / presets にPHPの初期値を埋め込む

`_inc/value/page/*.php` で定義されている初期テキスト・設定値は、
Liquid の `{% schema %}` 内の **`default`** と **`presets`** に埋め込んでおく。
これにより、テーマエディタを開いた時点で初期値が入った状態になる。

**例: FAQ セクション**

PHPの value:

```php
${'page_faq'} = defaultPageValue('faq', array(
    'title' => setValueTitle('Faq', "よくあるご質問"),
    'list' => array(
        array('title' => setValueTitle('洗うことはできますか？', ""), 'text' => '...'),
    ),
));
```

Liquid schema の presets に反映:

```json
"presets": [
  {
    "name": "Furfolk FAQ",
    "settings": {
      "title": "Faq",
      "subtitle": "よくあるご質問"
    },
    "blocks": [
      {
        "type": "faq_item",
        "settings": {
          "question": "洗うことはできますか？",
          "answer": "<p>内容を入力してください。</p>"
        }
      }
    ]
  }
]
```

#### default を入れるべき項目

| 項目                            | 入れる場所              | 元のPHP                              |
| ------------------------------- | ----------------------- | ------------------------------------ |
| セクション見出し（英語/日本語） | `settings` の `default` | `setValueTitle('English', '日本語')` |
| ボタンラベル                    | `settings` の `default` | `link.title`                         |
| カラー指定                      | `settings` の `default` | `'color' => 'color1'`                |
| FAQ の質問/回答                 | `presets` の `blocks`   | `list` 配列                          |
| フォームフィールド              | `presets` の `blocks`   | `form` の `fields` 配列              |

#### default を入れないべき項目

| 項目                      | 理由                                           |
| ------------------------- | ---------------------------------------------- |
| 画像                      | `image_picker` は default 不可。管理画面で設定 |
| URL                       | ストアごとに異なる。管理画面で設定             |
| コレクション/ブログの選択 | ストアごとに異なる                             |
| 商品固有のテキスト        | メタフィールドで管理                           |

### 8.2 templates/\*.json に初期ブロック構成を定義

テンプレートJSONの `blocks` と `block_order` に初期構成を書いておくと、
テーマを push した時点でテーマエディタに初期ブロックが並んだ状態になる。

**例: page.faq.json**

```json
{
  "sections": {
    "main": {
      "type": "d-faq",
      "settings": {
        "title": "Faq",
        "subtitle": "よくあるご質問"
      },
      "blocks": {
        "faq-1": {
          "type": "faq_item",
          "settings": { "question": "洗うことはできますか？", "answer": "<p>...</p>" }
        },
        "faq-2": {
          "type": "faq_item",
          "settings": { "question": "サイズはどうなっていますか？", "answer": "<p>...</p>" }
        }
      },
      "block_order": ["faq-1", "faq-2"]
    }
  },
  "order": ["main"]
}
```

### 8.3 settings_data.json にブランド情報をプリセット

`config/settings_data.json` にブランドカラー・SNSリンク等を事前に設定しておく。

```json
{
  "current": "案件名",
  "presets": {
    "案件名": {
      "color_schemes": {
        "scheme-1": { "settings": { "background": "#f6f2e2", "text": "#000000", ... } }
      },
      "social_instagram_link": "https://www.instagram.com/xxx/",
      "cart_type": "drawer",
      ...
    }
  }
}
```

### 8.4 header/footer-group.json にメニューハンドルを設定

`link_list` 型は `default` が使えないが、`*-group.json` の settings で初期値を指定できる。

```json
{
  "sections": {
    "header": {
      "type": "header",
      "settings": {
        "menu": "main-menu",
        "menu_sub": "sub-menu"
      }
    }
  }
}
```

### 8.5 PHP value からの変換チェックリスト

`_inc/value/page/*.php` の各ページを変換する際、以下を確認:

- [ ] `setValueTitle('English', '日本語')` → schema default に英語/日本語を設定
- [ ] `'color' => 'color1'` → schema default にカラー値を設定
- [ ] `'link' => array('title' => 'xxx', 'url' => '/path')` → ラベルのみ default、URLは空
- [ ] `'list' => array(...)` → presets の blocks に初期アイテムを定義
- [ ] `'text' => 'ダミーテキスト'` → ダミーはそのまま入れず、実際の文言がわかっていれば入れる
- [ ] `'image' => setValueImage(...)` → 画像は default 不可、スキップ
- [ ] `$commonSection` の値 → テンプレートJSON 内の settings に直接記述
- [ ] `$sectionTemplate` の値 → 複数セクションで使い回す値は settings_data.json に

---

## 9. Seed スクリプト（APIによる初期データ投入）

### 9.1 概要

管理画面の手動入力を減らすため、Shopify Admin API（GraphQL）でデータを一括登録するスクリプト群。
**メタオブジェクト定義・メタフィールド定義は必ずスクリプト化する**（フィールド数が多く手動だとミスしやすい）。
商品登録もデータ量が多い場合はスクリプト化を推奨。

### 9.2 使い分け

| 対象                 | 10件以下        | 10件以上       | 推奨                  |
| -------------------- | --------------- | -------------- | --------------------- |
| メタオブジェクト定義 | スクリプト      | スクリプト     | **常にスクリプト**    |
| メタフィールド定義   | スクリプト      | スクリプト     | **常にスクリプト**    |
| 商品                 | 管理画面 or CSV | **スクリプト** | データ量次第          |
| 固定ページ           | 管理画面        | スクリプト     | 少量なら手動          |
| コレクション         | 管理画面        | スクリプト     | 少量なら手動          |
| メニュー             | 管理画面        | 管理画面       | **手動**（API非推奨） |
| ブログ/記事          | 管理画面        | スクリプト     | データ量次第          |

### 9.3 ディレクトリ構成

```
scripts/
├── seed.mjs                  ← エントリポイント（全スクリプトを順に実行）
├── lib/
│   └── shopify-client.mjs    ← GraphQL クライアント初期化（共通）
├── seed-metaobjects.mjs      ← メタオブジェクト定義の作成（create-only + skip）
├── seed-metafields.mjs       ← メタフィールド定義の作成
├── seed-entries.mjs          ← メタオブジェクトエントリー + 商品LP紐づけ
├── seed-blogs.mjs            ← ブログ + 記事作成 + article_blocks紐づけ
├── seed-pages.mjs            ← 固定ページ作成 + テンプレート割り当て
└── data/
    ├── metaobjects.json      ← メタオブジェクト定義データ
    ├── metafields.json       ← メタフィールド定義データ（並び順 = 管理画面の表示順）
    ├── entries.json           ← メタオブジェクトエントリー + LP データ
    ├── blogs.json             ← ブログ + 記事 + article_blocks データ
    └── pages.json             ← 固定ページデータ
```

#### 実行順序（seed.mjs の中で順番に実行）

```
1. seed-metaobjects.mjs  ← 定義を先に作る
2. seed-metafields.mjs   ← 定義が存在する状態で参照を作る
3. seed-blogs.mjs        ← ブログ + 記事 + article_blocks
4. seed-entries.mjs      ← FAQ / 商品詳細 / LP エントリー
5. seed-pages.mjs        ← 固定ページ
```

### 9.3.1 スクリプトの特殊対応

#### seed-metaobjects.mjs

- **create-only**（既存定義は Skipped）。更新が必要な場合は管理画面で削除 → seed 再実行
- `list.metaobject_reference` / `metaobject_reference` のフィールドは `metaobjectType` で参照先を自動解決
- `validations` をJSON から直接渡せる（選択リスト等の choices 対応）

#### seed-metafields.mjs

- `list.metaobject_reference` / `metaobject_reference` の validations を自動解決
- `list.mixed_reference` は `metaobjectTypes`（複数）→ `metaobject_definition_ids` で一括指定
- **定義の並び順がShopify管理画面の表示順になる**

#### seed-entries.mjs

- `type: "product_lp"` のグループはネスト対応（子アイテム → セクション → lp_sections）
- `file_reference` フィールドはファイル名 → Files API で GID を自動解決（`read_files` スコープ必要）
- ファイルが見つからない場合はスキップ（管理画面で後から設定）
- `targetHandle: null` の場合はエントリー作成のみ（商品への紐づけはスキップ）

#### seed-blogs.mjs

- ブログ・記事は handle で検索して既存なら skip（べき等）
- Journal 記事の `article_blocks` → article_content メタオブジェクトを自動作成 + 紐づけ
- `file_reference` フィールドのファイル名 → Files API で GID を自動解決
- `author` フィールドは必須（デフォルト: `{ name: "{案件名}" }`）
- API の記事本文は `body`（`bodyHtml` ではない）

### 9.3.2 べき等性と再実行

| スクリプト       | 2回目以降の動作          | 再作成したい場合                            |
| ---------------- | ------------------------ | ------------------------------------------- |
| seed-metaobjects | Skipped                  | 管理画面で定義を削除 → 再実行               |
| seed-metafields  | Skipped                  | 管理画面でメタフィールド定義を削除 → 再実行 |
| seed-entries     | **エントリーが重複する** | 管理画面でエントリーを削除 → 再実行         |
| seed-blogs       | ブログ・記事は Skipped   | 管理画面で記事を削除 → 再実行               |

### 9.3.3 必要な API スコープ

| スコープ                      | 用途                           |
| ----------------------------- | ------------------------------ |
| `write_products`              | 商品・コレクション             |
| `write_content`               | ページ・ブログ・記事           |
| `write_metaobjects`           | メタオブジェクト定義・エントリ |
| `write_metafield_definitions` | メタフィールド定義             |
| `read_files`                  | Files API（画像 GID 解決）     |

### 9.4 前提条件

1. **カスタムアプリの作成が必要**
   管理画面 → 設定 → アプリと販売チャネル → アプリを開発 → カスタムアプリ作成
   API スコープは 9.3.3 を参照。

2. **環境変数**（`.env` に設定）

   ```
   SHOPIFY_STORE=your-store.myshopify.com
   SHOPIFY_ADMIN_TOKEN=shpat_xxxxx
   ```

3. **依存パッケージ**
   ```bash
   npm install @shopify/shopify-api dotenv
   ```

### 9.5 data/\*.json のフォーマット

#### metaobjects.json（メタオブジェクト定義）

実際の `scripts/data/metaobjects.json` を参照。主なパターン:

```json
// 基本（テキストのみ）
{ "key": "title", "name": "タイトル", "type": "single_line_text_field", "required": true }

// 画像
{ "key": "image", "name": "画像", "type": "file_reference" }

// テーブル（|区切り改行テキスト）
{ "key": "content", "name": "内容（項目名|項目値 を改行区切り）", "type": "multi_line_text_field" }

// 子メタオブジェクト参照（metaobjectType で参照先を指定）
{ "key": "items", "name": "項目", "type": "list.metaobject_reference", "metaobjectType": "lp_image_title_text" }

// バリアント参照
{ "key": "variants", "name": "関連商品", "type": "list.variant_reference" }
```

**注意: `rich_text_field` は使わない**（詳細はセクション 2.3 参照）

#### metafields.json（メタフィールド定義）

実際の `scripts/data/metafields.json` を参照。主なパターン:

```json
// 単純なテキスト
{ "ownerType": "PRODUCT", "namespace": "custom", "key": "name_kana", "name": "商品名カナ", "type": "single_line_text_field" }

// メタオブジェクト参照（metaobjectType でスクリプトが自動解決）
{ "ownerType": "PRODUCT", "namespace": "custom", "key": "detail_items", "name": "詳細情報", "type": "list.metaobject_reference", "metaobjectType": "product_detail" }

// mixed_reference（metaobjectTypes で複数型を指定）
{ "ownerType": "PRODUCT", "namespace": "custom", "key": "lp_sections", "name": "LP: セクション", "type": "list.mixed_reference", "metaobjectTypes": ["lp_section_features", "lp_section_use", ...] }

// バリアント用
{ "ownerType": "PRODUCTVARIANT", "namespace": "custom", "key": "name_kana", "name": "カナ表記", "type": "single_line_text_field" }
```

**定義の並び順 = 管理画面での表示順になる。** 重要なフィールドを先に配置すること。

#### pages.json（固定ページ）

```json
[
  { "title": "よくあるご質問", "handle": "faq", "templateSuffix": "faq", "body": "" },
  { "title": "お問い合わせ", "handle": "contact", "templateSuffix": "contact", "body": "" },
  { "title": "利用規約", "handle": "terms", "templateSuffix": "terms", "body": "<p>内容</p>" },
  { "title": "プライバシーポリシー", "handle": "privacy", "templateSuffix": "privacy", "body": "<p>内容</p>" },
  { "title": "特定商取引法に基づく表記", "handle": "legal", "templateSuffix": "legal", "body": "<p>内容</p>" },
  { "title": "ブランドストーリー", "handle": "story", "templateSuffix": "story", "body": "" }
]
```

#### products.json（商品）

```json
[
  {
    "title": "{商品名}",
    "handle": "{商品ハンドル}",
    "descriptionHtml": "<p>商品説明</p>",
    "vendor": "{案件名}",
    "productType": "Memorial",
    "tags": ["memorial"],
    "variants": [
      { "title": "Lion / Sand Beige / S", "price": "1650", "sku": "MONU-LION-SB-S", "inventoryQuantity": 10 },
      { "title": "Lion / Sand Beige / M", "price": "1650", "sku": "MONU-LION-SB-M", "inventoryQuantity": 0 }
    ],
    "metafields": [{ "namespace": "custom", "key": "name_kana", "type": "single_line_text_field", "value": "モニュ" }]
  }
]
```

### 9.6 実行フロー

```bash
# 1. メタオブジェクト・メタフィールド定義（最初に実行）
node scripts/seed-metaobjects.mjs

# 2. コレクション（商品より先に作る）
node scripts/seed-collections.mjs

# 3. 商品（コレクションが存在する状態で実行）
node scripts/seed-products.mjs

# 4. 固定ページ
node scripts/seed-pages.mjs

# 5. ブログ + 記事
node scripts/seed-blogs.mjs

# または全部まとめて
node scripts/seed.mjs
```

### 9.7 注意事項

- **べき等性**: 同じハンドルが存在する場合はスキップ or 更新する設計にすること
- **レートリミット**: Shopify API は 1秒2リクエスト制限あり。`await` + 間隔を空ける
- **画像**: 商品画像は URL 指定で登録可能（ローカルファイルは直接アップロード不可、Staged Upload API を使う）
- **メタオブジェクト定義のID**: メタフィールド定義で参照する場合、先に定義を作ってIDを取得する必要がある
- **テンプレート割り当て**: ページ作成時に `templateSuffix` を指定すれば API から割り当て可能（管理画面不要）
- **.shopifyignore に `scripts/` を追加すること**

---

## 10. PHP value → メタオブジェクト/メタフィールド変換ガイド

### 10.1 PHP のデータ構造を分析する

PHPの `_inc/value/page/*.php` を読み、各フィールドのデータ型を特定する:

| PHP のデータ型                              | Shopify メタフィールド/メタオブジェクト型                |
| ------------------------------------------- | -------------------------------------------------------- | -------------------- |
| `'テキスト'`（1行）                         | `single_line_text_field`                                 |
| `'テキスト\nテキスト'`（複数行）            | `multi_line_text_field`                                  |
| `setValueTitle('EN', 'JP')`                 | `single_line_text_field` × 2（title_en, title_jp）       |
| `setValueImage($path, 'alt')`               | `file_reference`                                         |
| `array(array(...), ...)` ループされるリスト | `list.metaobject_reference` → 子メタオブジェクト         |
| `'color1'` 等の列挙値                       | `single_line_text_field`（CSS の data-colortype と連携） |
| テーブル（th/td 配列）                      | `multi_line_text_field`（`項目名                         | 項目値` 改行区切り） |
| HTML テーブル（複数列）                     | `multi_line_text_field`（1行目が thead、`                | ` 区切り）           |
| 価格                                        | Shopify 商品の price フィールド                          |
| URL                                         | Shopify 商品/ページの url プロパティ                     |

### 10.2 データの持たせ方 判断フロー

```
このデータは全商品/全ページ共通？
  └── YES → section.settings
  └── NO → 商品/ページごとに違う
        │
        このデータは1つだけ？（画像1枚、テキスト1つ等）
        └── YES → 商品メタフィールド（single_line_text, file_reference 等）
        └── NO → リストが必要
              │
              リストの項目は同じ構造？
              └── YES → list.metaobject_reference → 共通メタオブジェクト
              └── NO → list.mixed_reference → セクション別メタオブジェクト
```

### 10.3 メタオブジェクト設計のルール

| ルール                                                      | 理由                                                          |
| ----------------------------------------------------------- | ------------------------------------------------------------- | --------------------- |
| rich_text_field は使わない                                  | WYSIWYG が `<p>` を自動挿入し、既存 CSS と競合する            |
| テーブルは `項目名                                          | 項目値` 改行区切り                                            | HTML 不要で入力できる |
| 1セクション = 1メタオブジェクト定義                         | 管理画面で不要なフィールドが表示されない                      |
| 異なるセクションの混在 → `list.mixed_reference`             | 必要なセクションだけ追加、順番も自由                          |
| 画像は `file_reference`                                     | API から直接設定不可（管理画面で手動設定）、seed では空にする |
| Liquid での改行 split は `newline_to_br \| split: '<br />'` | `split: "\n"` は Liquid で動かない                            |
| メタオブジェクトの type 取得は `.system.type`               | `.type` は空を返す                                            |

### 10.4 PHP value の変換例

#### 単純なテキスト（商品名カナ等）

**実装例（furfolk）:**

PHP:

```php
'name' => setValueTitle('monu', 'モニュ'),
```

Shopify:

- 商品タイトル: `monu`（Shopify 商品名）
- メタフィールド `custom.name_kana`: `モニュ`

#### リスト（FAQ 等）

PHP:

```php
'list' => array(
  array('title' => setValueTitle('質問', ''), 'text' => '回答'),
  array('title' => setValueTitle('質問2', ''), 'text' => '回答2'),
),
```

Shopify:

- メタオブジェクト `faq_item`: `question` + `answer`
- メタフィールド `custom.faq_items`: `list.metaobject_reference` → `faq_item`

#### ネストしたリスト（LP セクション）

PHP:

```php
'lp' => array(
  'contents' => array(
    'features' => array(
      'title' => setValueTitle('Features', '特徴'),
      'list' => array(
        array('image' => ..., 'lead' => '...', 'text' => '...'),
      ),
    ),
  ),
),
```

Shopify:

- 子メタオブジェクト `lp_image_title_text`: `image` + `title` + `text`
- セクションメタオブジェクト `lp_section_features`: `title_en` + `title_jp` + `color` + `items`（→ lp_image_title_text リスト）
- 商品メタフィールド `custom.lp_sections`: `list.mixed_reference`

#### テーブル（2列）

PHP:

```php
'table' => array(
  array('th' => '素材', 'td' => 'ポリエステル100%'),
  array('th' => '製造国', 'td' => '日本'),
),
```

Shopify メタオブジェクト content フィールド:

```
素材|ポリエステル100%
製造国|日本
```

Liquid で `newline_to_br | split: '<br />'` → `split: '|'` でテーブル生成。

#### テーブル（複数列・thead 付き）

PHP:

```php
'table2' => array(
  'th' => array('サイズ', '首周り', '胴周り'),
  'td' => array(
    array('th' => 'S', 'td' => array('15-26', '24-38')),
  ),
),
```

Shopify メタオブジェクト content フィールド:

```
サイズ|首周り|胴周り
S|15-26|24-38
```

1行目の `|` が3つ以上 → thead 付きテーブル（`b-item__detail__table2`）として自動判定。

### 10.5 seed スクリプト対応表

| PHP value の構造    | entries.json の形式                                          | seed 処理                                              |
| ------------------- | ------------------------------------------------------------ | ------------------------------------------------------ |
| 単純リスト（FAQ等） | `{ "metaobjectType": "faq_item", "entries": [...] }`         | createEntries → attachMetafield                        |
| ネストリスト（LP）  | `{ "type": "product_lp", "sections": [{ "items": {...} }] }` | 子 items → セクション → lp_sections                    |
| mixed_reference     | `metaobjectTypes` in metafields.json                         | `metaobject_definition_ids` validation                 |
| variant_reference   | `list.variant_reference` in metaobjects.json                 | 管理画面で手動設定                                     |
| file_reference      | 画像ファイル名（例: `pic-main_01.webp`）                     | Files API で GID 自動解決（`read_files` スコープ必要） |

### 10.6 メタオブジェクト定義一覧（furfolk 実装例）

| type                    | 名前                                 | 主なフィールド                                                                            | 用途                                           |
| ----------------------- | ------------------------------------ | ----------------------------------------------------------------------------------------- | ---------------------------------------------- |
| `faq_item`              | FAQ                                  | question, answer                                                                          | FAQ 項目                                       |
| `article_content`       | 記事コンテンツ                       | block_type, layout, width, box_option, title, description, image, caption, url, url_label | Journal 記事の box 構造                        |
| `product_detail`        | 商品詳細                             | title, content（\|区切り改行テキスト）, caption                                           | 商品詳細アコーディオン                         |
| `lp_image_title_text`   | LP項目（画像+タイトル+テキスト）     | image, title, text                                                                        | LP features/use/quality の子アイテム           |
| `lp_image_text_caption` | LP項目（画像+テキスト+キャプション） | image, text, caption                                                                      | LP gallery の子アイテム                        |
| `lp_section_features`   | LPセクション: 特徴                   | title_en, title_jp, color, items→lp_image_title_text                                      |                                                |
| `lp_section_use`        | LPセクション: 使用方法               | title_en, title_jp, color, items→lp_image_title_text                                      |                                                |
| `lp_section_quality`    | LPセクション: 品質                   | title_en, title_jp, color, items→lp_image_title_text                                      |                                                |
| `lp_section_variations` | LPセクション: カラー・タイプ         | title_en, title_jp, color                                                                 | バリアントから自動取得                         |
| `lp_section_sizeguide`  | LPセクション: サイズ詳細             | title_en, title_jp, color, image, table_content, attention                                |                                                |
| `lp_section_gallery`    | LPセクション: 着用イメージ           | title_en, title_jp, color, items→lp_image_text_caption                                    |                                                |
| `lp_section_review`     | LPセクション: お客様の声             | title_en, title_jp, color                                                                 | レビューアプリウィジェット（実装例: judge.me） |
| `lp_section_faq`        | LPセクション: よくある質問           | title_en, title_jp, color, items→faq_item                                                 |                                                |
| `lp_section_related`    | LPセクション: 関連商品               | title_en, title_jp, color, variants                                                       | list.variant_reference                         |

**color フィールド**: 選択リスト（choices: color1/color2/color3/color4）。CSSの `data-color` 属性と連携。

### 10.7 メタフィールド定義一覧（furfolk 実装例）

#### PRODUCT

| key                | 名前                         | 型                                         | 用途                                      |
| ------------------ | ---------------------------- | ------------------------------------------ | ----------------------------------------- |
| `detail_items`     | 詳細情報                     | list.metaobject_reference → product_detail | 商品詳細アコーディオン                    |
| `name_kana`        | 商品名カナ                   | single_line_text_field                     |                                           |
| `lead`             | リード文                     | multi_line_text_field                      |                                           |
| `text`             | 本文                         | multi_line_text_field                      | descriptionは使わない                     |
| `lp_head_image`    | LP: ヘッド画像               | file_reference                             |                                           |
| `lp_head_title_en` | LP: ヘッドタイトル（英語）   | single_line_text_field                     |                                           |
| `lp_head_title_jp` | LP: ヘッドタイトル（日本語） | single_line_text_field                     |                                           |
| `lp_head_text`     | LP: ヘッドテキスト           | multi_line_text_field                      |                                           |
| `lp_head_color`    | LP: ヘッドカラー             | single_line_text_field                     |                                           |
| `lp_sections`      | LP: セクション               | list.mixed_reference                       | 9種類のLPセクションを自由に追加・並べ替え |

#### PRODUCTVARIANT

| key          | 名前         | 型                     | 用途                      |
| ------------ | ------------ | ---------------------- | ------------------------- |
| `name_kana`  | カナ表記     | single_line_text_field | オプション値の日本語名    |
| `color_code` | カラーコード | single_line_text_field | カラーチップ用（#xxxxxx） |

#### その他

| owner      | key              | 型                                          | 用途           |
| ---------- | ---------------- | ------------------------------------------- | -------------- |
| COLLECTION | `title_sub`      | single_line_text_field                      | サブタイトル   |
| PAGE       | `faq_items`      | list.metaobject_reference → faq_item        | FAQページ用    |
| ARTICLE    | `article_blocks` | list.metaobject_reference → article_content | Journal 記事用 |

### 10.8 ダミーデータ投入（entries.json / blogs.json）

#### entries.json の構造

```json
{
  "faq_page": {
    "targetType": "PAGE",
    "targetHandle": "faq",
    "metafieldKey": "custom.faq_items",
    "metaobjectType": "faq_item",
    "entries": [
      { "question": "質問テキスト", "answer": "回答テキスト" }
    ]
  },
  "product_detail_sample": {
    "targetType": "PRODUCT",
    "targetHandle": null,
    "metafieldKey": "custom.detail_items",
    "metaobjectType": "product_detail",
    "entries": [
      { "title": "詳細", "content": "素材|ポリエステル100%\n製造国|日本", "caption": "注意書き" }
    ]
  },
  "lp_monu": {
    "type": "product_lp",
    "targetType": "PRODUCT",
    "targetHandle": null,
    "productMetafields": { "lp_head_title_en": "monu", ... },
    "sections": [
      {
        "metaobjectType": "lp_section_features",
        "fields": { "title_en": "Features", "title_jp": "特徴" },
        "items": {
          "metaobjectType": "lp_image_title_text",
          "metafieldKey": "items",
          "entries": [
            { "image": "pic-main_01.webp", "title": "タイトル", "text": "本文" }
          ]
        }
      }
    ]
  }
}
```

#### blogs.json の構造

```json
{
  "news": {
    "blog": { "title": "News", "handle": "news", "templateSuffix": "news" },
    "articles": [
      {
        "title": "記事タイトル",
        "handle": "test-news-1",
        "body": "<h2>見出し</h2><p>本文</p>",
        "tags": ["Event"],
        "published": true,
        "templateSuffix": "news"
      }
    ]
  },
  "journal": {
    "blog": { "title": "Journal", "handle": "journal", "templateSuffix": "journal" },
    "articles": [
      {
        "title": "記事タイトル",
        "handle": "test-journal-1",
        "body": "",
        "templateSuffix": "journal",
        "excerpt": "抜粋テキスト",
        "article_blocks": [
          {
            "block_type": "box",
            "layout": "column",
            "width": "default",
            "box_option": "captionFloat",
            "image": "pic-main_01.webp",
            "title": "見出し",
            "description": "本文",
            "caption": "キャプション",
            "url": "https://example.com/",
            "url_label": "詳しくはこちら"
          }
        ]
      }
    ]
  }
}
```

**注意:**

- `targetHandle: null` の場合、エントリー作成のみ（紐づけは手動）
- `image` フィールドはファイル名だけ指定。Files API で自動解決
- 2回目実行でエントリーが重複する。再投入する場合は管理画面でエントリーを削除してから

---

## 11. テーマ設定（\_variable.scss → config/settings_data.json）

### 11.1 カラー設定

`_src/scss/global/_variable.scss` の色定義を Shopify のカラースキームに反映する。

#### SCSS の色定義

`_src/scss/global/_variable.scss` のカラー定義を確認し、Shopify のカラースキームに反映する。

**実装例（furfolk）:**

```scss
--Key1: #ca9c8f; // nude
--Key2: #e4e0c5; // ecru
--Key3: #80bcc5; // turquoise
--Key4: #808980; // seaweed
--Base: #f6f2e2;
--Black: #222222;
--White: #ffffff;
```

#### Shopify カラースキーム対応表

`_variable.scss` のカラー定義に合わせて `config/settings_data.json` の scheme を設定する。

**実装例（furfolk）:**

| scheme   | 対応              | background | text    | button  | button_label |
| -------- | ----------------- | ---------- | ------- | ------- | ------------ |
| scheme-1 | Base（default）   | #f6f2e2    | #222222 | #222222 | #f6f2e2      |
| scheme-2 | Key1（nude）      | #ca9c8f    | #222222 | #222222 | #ffffff      |
| scheme-3 | Key2（ecru）      | #e4e0c5    | #222222 | #222222 | #ffffff      |
| scheme-4 | Key3（turquoise） | #80bcc5    | #222222 | #222222 | #ffffff      |
| scheme-5 | Key4（seaweed）   | #808980    | #ffffff | #ffffff | #808980      |

#### LP の data-colortype との対応

| data-colortype | SCSS 変数 | 背景色  |
| -------------- | --------- | ------- |
| default        | --Base    | #f6f2e2 |
| color1         | --Key1    | #ca9c8f |
| color2         | --Key2    | #e4e0c5 |
| color3         | --Key3    | #80bcc5 |
| color4         | --Key4    | #808980 |

LP セクションのメタオブジェクト `color` フィールドに `color1` 等を入力すると、
Liquid が `data-colortype="{{ color }}"` を出力し、SCSS の `[data-colortype="color1"]` で色が適用される。

### 11.2 フォント設定

```scss
--jp: YakuHanJP, "ヒラギノ角ゴ Pro W3", ...;
--en_go: "acumin-pro", sans-serif;
--en_min: "palatino-linotype", ...;
```

Shopify テーマ設定（settings_schema.json）ではフォント選択を提供できるが、
案件の CSS 変数でフォントを直接制御している場合、**Shopify のフォント設定は使わない**。
`{案件名}.css` 内の CSS 変数が全フォントを制御する。

Adobe Fonts（acumin-pro 等）は `layout/theme.liquid` の `<head>` 内で読み込むこと:

```html
<link rel="stylesheet" href="https://use.typekit.net/xxxxx.css" />
```

### 11.3 ブレイクポイント

```scss
$mediaTB: 1000;
$mediaSP: 680;
```

Shopify テーマ設定とは連携しない。`{案件名}.css` 内の `@media` で制御。

### 11.4 settings_data.json の更新

`config/settings_data.json` の `color_schemes` を SCSS に合わせて更新すること。
ただし Dawn の CSS はカスタムテーマでは使わないため、Shopify テーマエディタの見た目には影響するが、
実際の表示は `{案件名}.css` の CSS 変数が制御する。

---

## 12. 変換後チェックリスト

テーマ変換完了後、以下を順番に確認する。
**1つでも不一致があれば修正を完了としない。**

### 10.1 ビルド確認

```bash
npm run build
npx svg-sprite -C spriteConfig.json _src/icon/*svg
shopify theme dev
```

- [ ] ビルドエラーがないこと
- [ ] `assets/{案件名}.css` と `assets/{案件名}.js` が生成されていること
- [ ] `assets/sprite.svg` に必要な全アイコンが含まれていること
- [ ] `shopify theme dev` で Upload Errors がないこと

### 10.2 HTML出力の検証（ブラウザ開発者ツール）

#### 全ページ共通

- [ ] `<img src="...svg">` がどこにも存在しないこと（Elements タブで `img[src*=".svg"]` を検索）
- [ ] 全ロゴ・アイコンが `<svg><use xlink:href="...sprite.svg#...">` で出力されていること
- [ ] Dawn の CSS（`base.css`, `component-*.css`）が読み込まれていないこと（Network タブ）
- [ ] Dawn の `{% style %}` インラインスタイルが出力されていないこと
- [ ] `{案件名}.css` のみがスタイルを制御していること

#### ヘッダー

- [ ] `<header class="l-header">` が出力されていること
- [ ] `<nav class="l-header__nav">` が出力され、メニュー項目が表示されていること
- [ ] ナビの各項目に `__title` と `__subtitle` が出力されていること（`|` 区切り入力の場合）
- [ ] ロゴが `<span class="p-logo"><svg>...</svg><span>サイト名</span></span>` であること
- [ ] モーダル内ロゴが `<div class="p-logoMark"><svg>...#logo_mark</svg></div>` であること
- [ ] カートリンクが表示されていること
- [ ] ハンバーガーメニューが動作すること
- [ ] パンくずが正しいページ階層で表示されていること
- [ ] p-colorChanger の構造がPHP版と一致すること:
  - `__main` 内に `svg.is-block` と `svg.is-logo` があること
  - `__list` 内の各ボタンに `svg.is-check` と `svg.is-logo` があること

#### フッター

- [ ] `<footer class="l-footer">` が出力されていること
- [ ] ロゴが `<span class="p-logo__vertical"><svg>...#logo_vertical</svg>` であること
- [ ] フッターメニュー1, 2 が表示されていること
- [ ] フッターサブメニューが表示されていること
- [ ] SNSリンクが表示されていること（sprite アイコン経由）
- [ ] コピーライトが表示されていること

#### トップページ

- [ ] MV スライドショーが表示されていること
- [ ] Brand Story セクションのテキスト・画像が表示されていること
- [ ] Products セクションのコレクション商品が表示されていること
- [ ] Studio セクションが表示されていること
- [ ] Journal スライダーにブログ記事が表示されていること
- [ ] News スライダーにブログ記事が表示されていること
- [ ] Instagram セクションが表示されていること

#### 商品ページ

- [ ] 商品画像の Splide ギャラリー（メイン + サムネイル）が動作すること
- [ ] 商品名・カナ・価格が表示されていること
- [ ] バリアントオプションが選択可能であること
- [ ] カート追加ボタンが動作すること
- [ ] メタオブジェクト（content_blocks）のブロックが表示されていること（設定時）
- [ ] FAQ（faq_items メタオブジェクト）のアコーディオンが動作すること（設定時）
- [ ] 関連商品が表示されていること

#### コレクション（商品一覧）ページ

- [ ] コレクションタイトル（main + sub）が表示されていること
- [ ] カテゴリナビゲーションが表示されていること
- [ ] 商品カードのグリッドが表示されていること
- [ ] ページネーションが動作すること

#### FAQページ

- [ ] ページタイトルが表示されていること
- [ ] メタオブジェクト（faq_items）から FAQ 項目が表示されていること
- [ ] アコーディオンが開閉すること

#### お問い合わせページ

- [ ] ページタイトルが表示されていること
- [ ] フォームフィールドが表示されていること
- [ ] 送信が動作すること

#### 静的ページ（Terms, Privacy, Legal, Story）

- [ ] ページタイトルが表示されていること
- [ ] `page.content`（WYSIWYG）の内容が `b-body` 内に表示されていること

#### ブログ一覧（News / Journal）

- [ ] ブログタイトルが表示されていること
- [ ] 記事一覧が表示されていること（画像・日付・タイトル）
- [ ] ページネーションが動作すること

#### 記事詳細

- [ ] 記事タイトル・日付・タグが表示されていること
- [ ] メタオブジェクト（article_blocks）がある場合はブロック表示されること
- [ ] メタオブジェクトがない場合は `article.content`（WYSIWYG）が表示されること
- [ ] 「一覧に戻る」リンクが動作すること

### 10.3 CSS クラスの検証

PHP版のページと比較して、以下のクラスが正しく出力されているか確認:

- [ ] `p-text` — テキスト要素に付いていること
- [ ] `p-lead` — リード文に付いていること
- [ ] `p-price` — 価格に付いていること
- [ ] `p-date` — 日付に付いていること
- [ ] `p-button` — ボタンリンクに付いていること
- [ ] `p-image` — 画像要素に付いていること
- [ ] `p-title__*` — タイトルに適切な size サフィックスが付いていること
- [ ] `b-faq__dl` — FAQ のアコーディオンに付いていること
- [ ] `b-body` — WYSIWYG コンテンツのラッパーに付いていること
- [ ] `section` — 各セクションの外側に付いていること
- [ ] `is-narrow` — ナロー幅セクションに付いていること

### 10.4 PHP版との差分比較方法

PHP版の開発環境が動作する場合、以下の手順で diff を取る:

1. PHP版でページを開き、ブラウザの開発者ツール → Elements → `<body>` を右クリック → Copy → Copy outerHTML
2. `php_output.html` として保存
3. Shopify版で同じページを開き、同様にコピー → `shopify_output.html` として保存
4. diff で比較:

```bash
# Shopify のラッパー div を除去して比較
diff <(cat php_output.html | sed 's/shopify-section[^"]*//g') shopify_output.html
```

**主に確認すべき差分:**

- タグ名の違い（`<div>` vs `<p>` 等）
- class 名の欠落
- 属性の欠落（`data-splide`, `data-color` 等）
- ネスト構造の違い（余分な `<span>` や `<div>` がある/ない）
- SVG の `<use>` が `<img>` になっていないか

**許容される差分:**

- `shopify-section` ラッパー div（Shopify が自動追加）
- CDN URL の違い（`/assets/` vs `/cdn/shop/...`）
- Shopify が挿入するトラッキングスクリプト
- `{{ content_for_header }}` の出力内容
