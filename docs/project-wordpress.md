# WordPress プロジェクト固有ルール

このファイルは CLAUDE.md の共通ルールを補完する。
WordPress + PHP + Vite 構成のプロジェクトでのみ適用。

---

## ファイル構造とビルドフロー

**`_src/` が唯一の編集対象。`_public/` と `_wp/` は自動生成なので直接編集禁止。**

```
_src/  →  (コピー/コンパイル)  →  _public/  →  (コピー)  →  _wp/themes/assets/
```

| 対象                 | 説明                                                                            |
| -------------------- | ------------------------------------------------------------------------------- |
| `_src/`              | **編集する** — PHP・SCSS・JS・画像・アイコンすべてのソース                      |
| `_public/`           | **触らない** — `_src/` からのコピー＋ Vite ビルド出力。次のビルドで上書きされる |
| `_wp/themes/assets/` | **触らない** — `_public/assets/` からのさらなるコピー。ビルドで上書きされる     |

### コンパイル対象の対応表

| ソース（\_src/）    | 出力先（\_public/）            | 処理                                                          |
| ------------------- | ------------------------------ | ------------------------------------------------------------- |
| `html/**/*.php`     | `html/` 直下にコピー           | そのままコピー                                                |
| `_inc/**/*.php`     | `assets/inc/`                  | そのままコピー                                                |
| `scss/` → `main.js` | `assets/css/style.css`         | Vite + Sass + PostCSS                                         |
| `js/` → `main.js`   | `assets/js/index.js`           | Vite バンドル                                                 |
| `image/`            | `assets/image/`                | scripts/sharp.mjs で圧縮 + WebP 生成（`.env` の `WEBP_ONLY` で制御） |
| `icon/*.svg`        | `assets/image/icon/sprite.svg` | svg-sprite でスプライト化                                     |
| `files/`            | `assets/files/`                | そのままコピー                                                |

### AI への指示

- **PHP・SCSS・JS の修正は必ず `_src/` 内のファイルに対して行う**
- `_public/` や `_wp/` のファイルを直接編集しても、次のビルドで上書きされるため無意味
- ビルド出力の確認が必要な場合は `npm run build` または `npm run dev` を実行

---

## 仕様の正本

実装・修正時は以下を参照する：

- `_src/_inc/value/page/*.php` — 各ページのデータ定義
- `_src/_inc/function/index.php` — 共通関数・ヘルパー
- `_src/_inc/function/parts/*.php` — すべてのパーツ・コンポーネント（p-\*、blocks/ 廃止済み）

**注意**:

- 不明点は実装前に質問する
- 仕様に曖昧な点があれば質問する

---

## 参照整合性チェック(修正時の必須確認)

修正・実装後、以下を必ず確認する。**1つでも不一致があれば修正を完了としない。**

### value/page/\*.php の二重構造

各ページの value ファイルは 2 つの役割を持つ：

1. **静的データ定義** — `defaultPageValue()` / `$cpt_list` でローカル表示用ダミーデータ
2. **ACF フィールド定義** — `addAcfValueArray()` + `setAcf*()` で WordPress カスタムフィールドを登録

**両者のキー名が一致していないと、WP 環境でデータが取得できない。**

### チェックリスト

#### 静的データ ↔ ACF フィールド

- [ ] 静的データのキー名（`'title'`, `'message'`, `'postList'` 等）と `setAcf*()` の `$name` 引数が一致しているか
- [ ] 静的データの構造（group / repeater / flexible content）と ACF のフィールドタイプが対応しているか
- [ ] `addAcfValueArray()` の `$type`（`'archive'` / `'single'` / `'page'`）がページ種別と一致しているか

#### PHP テンプレート ↔ データ定義

- [ ] `$this_page_value` から参照するキーが `value/page/*.php` の定義と一致しているか
- [ ] `setHtml*()` に渡す引数の構造が、対応する `setAcf*()` の出力構造と一致しているか

#### CSS クラス命名

- [ ] すべてのコンポーネント（`function/parts/`）は `p-` 接頭語になっているか（`b-` 接頭語は廃止）
- [ ] `setHtml*()` の出力するクラス名が SCSS 側の定義と一致しているか
- [ ] ページ固有のスタイルが `layout/page/_*.scss` に分離されているか

---

## CSS クラス命名規則

| ディレクトリ      | 接頭語 | SCSS ファイル              | 定義                                     |
| ----------------- | ------ | -------------------------- | ---------------------------------------- |
| `function/parts/` | `p-`   | `_parts.scss` / `_blocks.scss` | すべてのパーツ・コンポーネント（parts/に統合済み） |

> **注意**: `function/blocks/` は廃止済み。すべてのコンポーネントは `function/parts/` に統合され、CSS クラスは `p-` 接頭語に統一されている（`_blocks.scss` は引き続きスタイル定義に使用）。

詳細な命名規則・パイプルールは「## SCSS ルール」セクションを参照。

---

## 共通コンポーネント(setHtml 関数)

詳細な引数仕様・使用例は仕様の正本（`function/parts/*.php`）を参照。

### 基本パーツ（`_parts.scss`）

| 関数                                          | 用途                                |
| --------------------------------------------- | ----------------------------------- |
| setHtmlTitle                                  | 見出し（main/sub/icon）             |
| setHtmlText                                   | テキスト                            |
| setHtmlImage / setHtmlBgImage                 | 画像                                |
| setHtmlLink / setHtmlLinks                    | リンク                              |
| setHtmlSvg                                    | SVG アイコン                        |
| setHtmlDl                                     | データリスト（type: dl/faq）        |
| setHtmlFaq                                    | FAQ（setHtmlDl の faq ラッパー）    |
| setHtmlUl / setHtmlOl / setHtmlLi             | 箇条書きリスト                      |
| setHtmlTable                                  | テーブル                            |
| setHtmlPankuzu                                | パンくず                            |
| setHtmlTaxonomy                               | タクソノミーラベル                  |
| setHtmlFilter                                 | フィルター（p-filter / p-checkbox） |
| setHtmlVideo / setHtmlYoutube                 | 動画                                |
| setHtmlNote                                   | 注釈・補足ボックス（type: note/info/warning/caution） |
| setHtmlCv / setHtmlTel / setHtmlSns           | CTA・電話・SNS                      |

### 複合コンポーネント（`_blocks.scss`）

| 関数                                            | 用途                                                         |
| ----------------------------------------------- | ------------------------------------------------------------ |
| setHtmlMv                                       | メインビジュアル                                             |
| setHtmlBody / setHtmlContentsBox                | 本文ブロック                                                 |
| setHtmlBox                                      | カード（image/date/taxonomy/title/text/link）                |
| setHtmlTextBox / setHtmlTextBoxList             | テキストボックス（plain/number/icon リスト対応）             |
| setHtmlImageBox                                 | イメージボックス（image/title/text/link）                    |
| setHtmlHistory                                  | 沿革（year＞list 2階層構造）                                 |
| setHtmlChart                                    | グラフ（Chart.js / `<canvas class="js-chart" data-chart>` ） |
| setHtmlSidenav                                  | セクションアンカーナビ                                       |
| setHtmlNav / setHtmlBigNav / setHtmlNavTaxonomy | ナビゲーション                                               |
| setHtmlSplide                                   | スライダー                                                   |
| setHtmlForm                                     | フォーム                                                     |
| setHtmlRecruitTable                             | 採用情報テーブル                                             |
| setHtmlProfile                                  | プロフィール（image/pos/title/text/career）                  |
| setHtmlWysiwyg                                  | WYSIWYG                                                      |

### Box 系コンポーネントの使い分け

`setHtmlBox` / `setHtmlImageBox` / `setHtmlTextBox` は類似した構成要素を持つが、**用途と要素の並び順が異なる**。それぞれの個性として維持しており、共通化はしない（「三つ似た行があるほうが早すぎる抽象化より良い」原則）。新規ページ実装時は下表を参考に選ぶ。

| 関数 | 要素の並び | 主用途 |
| --- | --- | --- |
| `setHtmlBox` | image / date / taxonomy / title / text / link | 記事カード・投稿一覧（メタ情報付き） |
| `setHtmlImageBox` | image / title / text / link（image 先行） | ビジュアル主導のカード・特集 |
| `setHtmlTextBox` | title / text / link（画像なし） | テキスト主導のリスト・補足ブロック |

#### 選択フロー

1. 日付やカテゴリ等のメタ情報を伴う投稿カードか? → **`setHtmlBox`**
2. 画像を強調してビジュアル主導で見せたいか? → **`setHtmlImageBox`**
3. テキストのみで画像は不要か? → **`setHtmlTextBox`**

いずれにも該当しない要素構成が必要な場合は、既存を拡張せず、**新しい複合コンポーネント関数**（例: `setHtmlProfileBox`）として追加することを検討する。

---

## プロジェクト固有プロンプト

### プレフィックス整合性チェック

```
全SCSSファイルで p- / b- / l- / page- 以外のプレフィックスが
残っていないか確認してください。
```

### 新規ページ追加(静的サイト)

```
「○○」ページを追加したいです。

1. sitemap.json に追加
2. _src/html/○○/index.php を作成
3. _src/scss/layout/page/_○○.scss を作成
4. 必要なコンポーネントを提案してください
```

### 新規ページ追加(WordPress)

```
「○○」ページを WordPress で追加したいです。

1. sitemap.json に投稿タイプとして追加
2. _src/_inc/value/page/○○.php を作成（静的データ + ACF定義）
3. _src/_inc/page/○○.php を作成
4. 必要な ACF フィールドを提案してください
```

### デプロイ前チェック

```
本番デプロイ前のチェックを実行してください。

1. @fix コメントが残っていないか
2. console.log が残っていないか
3. 開発用コード（_dev.scss 等）が含まれていないか
4. 画像が最適化されているか
```

---

## デバッグ

### debug.log の確認

```bash
docker exec xxxxxxxx-web_wordpress-1 cat /var/www/html/wp-content/debug.log
```

---

## 環境セットアップ

```bash
# 依存関係のインストール
npm install

# 開発サーバー起動
npm run dev

# 本番ビルド
npm run build
```

### WordPress 開発の場合

```bash
# Docker でローカル環境起動
docker compose up

# sitemap.json をもとにカスタム投稿タイプ（CPT）を確認
```

---

## html/ と _inc/page/ の命名対応

同じページを指す 2 つのファイルが異なる命名規則を持つ点に注意。

- `_src/html/` 側 — **ディレクトリ階層**で表現（`/` 区切り）
- `_src/_inc/page/` 側 — **アンダースコア連結**で表現（`_` 区切り）

| 階層 | `_src/html/` 側 | `_src/_inc/page/` 側 |
|------|------------------------------------|---------------------------------|
| 単階層 | `about/index.php` | `about.php` |
| 2 階層 | `company/message/index.php` | `company_message.php` |
| 3 階層 | `company/outline/history/index.php` | `company_outline_history.php` |
| 投稿タイプ（一覧） | `news/index.php` | `news_archive.php` |
| 投稿タイプ（詳細） | `news/detail.php` | `news_single.php` |

### 変換ルール

- `html/` 側のディレクトリ区切り（`/`）は `_inc/page/` 側ではアンダースコア（`_`）に置換される
- 投稿タイプ（`post_type: "post"`）の場合、`_inc/page/` 側は `{slug}_archive.php`（一覧）と `{slug}_single.php`（詳細）の 2 ファイルに分かれる
- これらの変換は `npm run generate`（[generate-pages.mjs](../scripts/generate-pages.mjs)）が `sitemap.json` の定義から自動生成する
- 新規ページ追加時は `sitemap.json` を編集して `npm run generate` を実行するだけで、両側のひな型が命名規則どおりに揃う

---

## ビルドパイプライン詳細

### npm スクリプト

| コマンド | 処理内容 |
| --- | --- |
| `npm run dev` | PHP サーバー + Vite HMR + ファイル監視を同時起動 |
| `npm run build` | 本番ビルド（クリーン → Vite → PHP/画像/SVG コピー → WP同期） |
| `npm run build:vite` | SCSS/JS のみコンパイル |
| `npm run generate` | `sitemap.json` からページひな型を自動生成 |

### ビルド処理の詳細(`npm run build`)

```
1. clean         — _public/assets/{css,js,image} を削除
2. build:vite    — Vite で SCSS → CSS, JS → バンドル
3. php           — _src/html/ → _public/ にコピー
4. inc           — _src/_inc/ → _public/assets/inc/ にコピー
5. imagemin      — _src/image/ → scripts/sharp.mjs で圧縮 + WebP 生成
6. svgsprite     — _src/icon/*.svg → スプライト化
7. file          — _src/files/ → _public/assets/files/ にコピー
8. wpassets      — _public/assets/ → _wp/themes/assets/ に同期
```

### 開発サーバー(`npm run dev`)

3 つのプロセスが同時起動:

| プロセス | ポート | 役割 |
| --- | --- | --- |
| PHP ビルトインサーバー | 8080(自動検出) | ページ配信。ブラウザの入口 |
| Vite dev サーバー | 5173(自動検出) | HMR(CSS/JS の即時反映) |
| ファイルウォッチャー | — | PHP・画像・SVG 等の変更を検知してコピー |

ポート情報は `.ports.json` に保存され、PHP 側が Vite の接続先を動的に解決する。

### ファイル監視(watch)対応表

| 監視対象（_src/） | コピー先（_public/） | 処理 |
| --- | --- | --- |
| `_inc/**/*` | `assets/inc/` | 差分コピー |
| `html/**/*` | ルート直下 | 差分コピー |
| `_inc/page/**/*` | `assets/inc/` | 差分コピー |
| `icon/**/*` | — | svg-sprite 再実行 |
| `image/**/*` | `assets/image/` | scripts/sharp.mjs(差分処理) |
| `files/**/*` | `assets/files/` | 差分コピー |

### 画像処理(scripts/sharp.mjs)

`.env` の `WEBP_ONLY` で出力形式を切り替え(デフォルト: `true`)。

| 設定 | 入力 `.jpg` / `.jpeg` / `.png` | 備考 |
| --- | --- | --- |
| `WEBP_ONLY=true` | `.webp` のみ出力 | デフォルト。元形式は出力しない |
| `WEBP_ONLY=false` | 元形式 + `.webp` 両方出力 | 従来動作 |

`process.env.WEBP_ONLY` で一時的に上書き可能(例: `WEBP_ONLY=false npm run imagemin`)。

| 入力形式 | 圧縮設定 |
| --- | --- |
| `.jpg` / `.jpeg` | JPG: quality 90 / WebP: quality 90 |
| `.png` | PNG: compression 9 / WebP: lossless |
| `.svg` / `.ico` | そのままコピー(WEBP_ONLY の影響なし) |

---

## SCSS 構造

### コンパイルフロー

```
_src/main.js
  ↓ import
_src/scss/style.scss
  ↓ @use
  ├── global/   (_variable, _mixin, _extend, _initialize, _parts, _blocks, _js)
  ├── layout/   (_header, _footer, _main, _adjust, page/_*.scss)
  └── dev/      (_dev, _guide, _template)
  ↓
Vite + sass コンパイル
  ↓
PostCSS(autoprefixer, media-queries sort, discard-comments)
  ↓
_public/assets/css/style.css
```

### ディレクトリ

```
_src/scss/
├── style.scss          # メインファイル(global, layout, dev をインポート)
├── global/             # グローバル定義
│   ├── _variable.scss # 変数・カラー・グリッド・サイズ
│   ├── _mixin.scss    # ミックスイン(メディアクエリ、flex 等)
│   ├── _extend.scss   # 拡張・ユーティリティ
│   ├── _initialize.scss # リセット・基本スタイル
│   ├── _parts.scss    # パーツ(p-*)
│   ├── _blocks.scss   # ブロック(b-*)
│   └── _js.scss       # JavaScript 連携クラス(is-*, js-*)
├── layout/             # レイアウト
│   ├── _header.scss
│   ├── _footer.scss
│   ├── _main.scss
│   ├── _adjust.scss   # 微調整用
│   └── page/          # 各ページ固有のスタイル
│       ├── _top.scss
│       ├── _about.scss
│       └── ...
└── dev/                # 開発用(本番には含めない)
    ├── _dev.scss
    ├── _guide.scss
    └── _template.scss
```

---

## SCSS ルール

### パイプルール

**原則: HTML のネスト構造と同じ順序で `&` を使って書く。**

SCSS のネストが HTML の親子関係と対応することで、HTML を見れば SCSS の場所がわかり、SCSS を見れば HTML の構造が読める。

```
HTML構造:                         SCSS構造:
<div class="b-nav">               .b-nav {
  <ul class="b-nav__ul">            &__ul { }
    <li class="b-nav__li">          &__li {
      <a class="b-nav__link">         &.is-active { }
        <span>                       }
        <span class="b-nav__icon">   &__link {
      </a>                             &:hover { }
    </li>                            }
  </ul>                              &__icon { }
</div>                             }
```

### 具体例

```scss
// parts
.p-card {
  &__image { }
  &__title { }
  &__text { }
}

// blocks
.b-nav {
  &__ul { }
  &__li {
    &.has-child { }
    &.is-active { }
  }
  &__link {
    &:hover { }
  }
  &__icon { }
  &__child { }
}

// page
.page-top {
  &__hero { }
  &__feature { }
  &__news { }
}
```

### 悪い例

```scss
// フラット記法(HTML 構造が読めない)
.p-card { }
.p-card__image { }
.p-card__title { }

// 深すぎるネスト(Element の中に Element を入れない)
.b-nav {
  &__ul {
    &__li { }    // ← .b-nav__ul__li になる。&__li にすべき
  }
}
```

### is- クラスによる分岐

コンポーネントのバリエーション(色・サイズ・方向・タイプ)は **BEM の `--Modifier` ではなく `is-` クラス**で分岐する。

#### パターン

```
.{component}.is-{category}__{variant}
```

| カテゴリ | 用途 | 例 |
|----------|------|-----|
| `is-color` | カラー分岐 | `is-color__sub`, `is-color__white`, `is-color__key1` |
| `is-size` | サイズ分岐 | `is-size__large`, `is-size__small` |
| `is-reverse` | 方向反転 | `is-reverse`(単独) |
| `is-image` | タイプ切替 | `is-image`(単独) |
| `is-active` | 状態 | `is-active`, `is-open`(JS 制御) |
| `is-invert` | 色反転 | `is-invert`(Basic / Invert 切替) |

#### SCSS の書き方

```scss
.p-button {
  // デフォルトスタイル

  &.is-color {
    &__sub { }       // → .p-button.is-color__sub
    &__white { }     // → .p-button.is-color__white
  }
  &.is-size {
    &__large { }     // → .p-button.is-size__large
    &__small { }     // → .p-button.is-size__small
  }
  &.is-reverse { }   // → .p-button.is-reverse
}
```

#### HTML での使い方

```html
<!-- デフォルト -->
<a class="p-button"><span>ボタン</span></a>

<!-- カラー分岐 -->
<a class="p-button is-color__sub"><span>ボタン</span></a>

<!-- サイズ + カラー -->
<a class="p-button is-size__large is-color__white"><span>ボタン</span></a>
```

### 変数・設計体系(\_variable.scss)

具体的な値は `_src/scss/global/_variable.scss` を正本とする。以下は設計体系の概要。

#### カラー

CSS カスタムプロパティで管理。案件ごとに値を変更する。

| 変数 | 用途 |
|------|------|
| `--Key1`, `--Key2`, `--Key3` | キーカラー(メイン・サブ・アクセント) |
| `--Title`, `--Text`, `--SubText` | テキスト色 |
| `--Border`, `--Base1~3`, `--White` | UI 色 |
| `--Attention` | エラー・警告 |

`is-invert` / `is-sectionInvert` で Basic / Invert(ダーク)の色セットが切り替わる。

#### フォントサイズ

べき乗スケール `--pow0` 〜 `--pow10`(基準値 x 1.2^n)。レスポンシブ対応済み。

#### ブレークポイント

| 変数 | 値 | 使い方 |
|------|-----|--------|
| `$mediaTB` | 1000px | `@include global.media(tb)` |
| `$mediaSP` | 680px | `@include global.media(sp)` |

#### グリッド

12 カラムグリッド。`gridSize($n)` 関数でカラム数からサイズを算出。

#### サイズ

| 変数 | 用途 |
|------|------|
| `--contentMaxWidth` | コンテンツ最大幅 |
| `--commonWidth` | コンテンツ幅(レスポンシブ) |
| `--sectionPadding` | セクション余白 |
| `--buttonWidth` | ボタン幅 |

### ミックスイン(\_mixin.scss)

具体的な定義は `_src/scss/global/_mixin.scss` を正本とする。

```scss
// レスポンシブ
@include global.media(sp) { }
@include global.media(tb) { }
@include global.media(pc) { }

// フレックス
@include global.flex(center, center);

// フォント一括指定
@include global.font(var(--pow0), var(--fwn), var(--ls), 2, var(--ff));

// ボタンカラー(背景色, 罫線色, 文字色, ホバー背景色, ホバー罫線色, ホバー文字色)
@include global.ButtonColor(var(--Key1), var(--Key1), var(--White), transparent, var(--Key1), var(--Key1));

// アスペクト比
@include global.ratio($pc: math.div(3, 2));
```

---

## コーディング規約

### HTML

- **インデント**: 2 スペース
- **属性の順序**: `class` → `id` → `data-*` → その他
- **閉じタグ**: 必ず記述
- **セマンティック HTML**: 意味のあるタグを使用(`<section>`, `<article>`, `<nav>` 等)
- **見出し階層**: h1 → h2 → h3 の順。階層を飛ばさない

### CSS / SCSS

- **インデント**: 2 スペース
- **セミコロン**: 必ず記述
- **クォート**: シングルクォート(`'`)
- **色指定**: 小文字(`#fff`, `#000`)
- **ネストの深さ**: 最大 3 階層まで
- **変数を使う**: マジックナンバーを避ける

#### プロパティの順序

```scss
.element {
  /* 1. 配置 */
  position: relative;
  top: 0;
  z-index: 1;

  /* 2. ボックスモデル */
  display: block;
  width: 100%;
  margin: 0;
  padding: 0;

  /* 3. 背景・ボーダー */
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 4px;

  /* 4. テキスト */
  font-size: 16px;
  line-height: 1.5;
  color: #333;

  /* 5. その他 */
  opacity: 1;
  transition: all 0.3s ease;
}
```

### JavaScript

- **インデント**: 2 スペース
- **セミコロン**: 必ず記述
- **クォート**: シングルクォート(`'`)
- **const/let**: `var` は使わない
- **アロー関数**: 推奨

| 種類 | 形式 | 例 |
|------|------|-----|
| 変数・関数 | camelCase | `userName`, `getUserName()` |
| 定数 | UPPER_SNAKE_CASE | `MAX_COUNT`, `API_URL` |
| クラス | PascalCase | `UserData`, `ApiClient` |

### PHP

- **インデント**: 2 スペース
- **PHP タグ**: `<?php ?>` を使用(短縮形 `<? ?>` は禁止)
- **エスケープ**: セキュリティ対策として必須

| 関数 | 用途 |
|------|------|
| `esc_html()` | HTML テキストのエスケープ |
| `esc_attr()` | HTML 属性のエスケープ |
| `esc_url()` | URL のエスケープ |
| `wp_kses_post()` | 投稿 HTML のサニタイズ |

| 種類 | 形式 | 例 |
|------|------|-----|
| 変数 | camelCase | `$userName`, `$postData` |
| 定数 | UPPER_SNAKE_CASE | `MAX_COUNT`, `API_URL` |

### ファイル命名規則

- **小文字**: すべて小文字
- **区切り文字**: ハイフン(`-`)またはアンダースコア(`_`)

| ファイル種類 | 例 |
|------------|-----|
| SCSS ファイル | `_header.scss`, `_button.scss` |
| JavaScript ファイル | `main.js`, `scroll.js` |
| PHP ファイル | `top.php`, `about.php` |
| 画像ファイル | `logo-header.svg`, `hero-bg.jpg` |

---

## WordPress 構造 / CPT / ACF 詳細

このセクション以降は WordPress プロジェクトにおける構造・カスタム投稿タイプ（CPT）・Advanced Custom Fields（ACF）の詳細リファレンス。

---

## プロジェクト構造

### WordPress環境

```
_wp/
├── plugins/              # プラグイン
│   ├── advanced-custom-fields/  # ACF
│   ├── all-in-one-wp-migration/ # データ移行
│   └── ...
└── themes/               # テーマ（ビルド後に配置）
```

### ビルドフロー

```
_src/                     # ソースファイル（開発）
    ↓ npm run build
_public/                  # 静的サイト（ビルド後）
_wp/themes/               # WordPressテーマ（ビルド後）
```

---

## カスタム投稿タイプ（CPT）

`sitemap.json`の`post_type`に基づいて自動生成されます。

### post_typeの種類

| post_type | 意味         | 用途                               | 管理画面   |
| --------- | ------------ | ---------------------------------- | ---------- |
| `top`     | トップページ | ホーム、セクションTOP              | 固定ページ |
| `page`    | 固定ページ   | 会社概要、プライバシーポリシーなど | 固定ページ |
| `post`    | 投稿（CPT）  | お知らせ、事例、サービスなど       | 投稿タイプ |
| `form`    | フォーム     | お問い合わせ、応募フォーム         | 固定ページ |

### 投稿タイプの例

#### お知らせ（news）

```json
{
  "name": "お知らせ",
  "slug": "news",
  "post_type": "post",
  "taxonomy": [
    {
      "name": "カテゴリ",
      "slug": "category",
      "category": true,
      "value": [
        ["カテゴリ1", "category1"],
        ["カテゴリ2", "category2"]
      ]
    }
  ]
}
```

**WordPress管理画面での表示**：

- 投稿タイプ名：「お知らせ」
- スラッグ：`news`
- タクソノミー：「カテゴリ」（category1, category2...）

#### 事例紹介（case）

```json
{
  "name": "事例紹介",
  "slug": "case",
  "post_type": "post",
  "taxonomy": [
    {
      "name": "カテゴリ",
      "slug": "category",
      "category": true,
      "value": [...]
    }
  ]
}
```

---

## タクソノミー（分類）

投稿タイプには、カテゴリやタグを設定できます。

### カテゴリ（階層あり）

```json
{
  "name": "カテゴリ",
  "slug": "category",
  "category": true, // カテゴリとして扱う
  "value": [
    ["お知らせ", "news"],
    ["プレスリリース", "press"],
    ["イベント", "event"]
  ]
}
```

### タグ（階層なし）

```json
{
  "name": "タグ",
  "slug": "tag",
  "category": false, // タグとして扱う
  "value": [
    ["重要", "important"],
    ["新製品", "new-product"],
    ["キャンペーン", "campaign"]
  ]
}
```

---

## Advanced Custom Fields（ACF）

### ACFの役割

WordPress のカスタムフィールドを管理するプラグイン。このテンプレートでは **ACF管理画面ではなく、value/page/\*.php で PHP配列として定義**する。

### valueファイルの場所

```
_src/_inc/value/
├── _common.php          # グローバル変数（サイト名、住所、連絡先等）
├── ja.php               # 日本語用の値
├── page/                # ページ別データ定義
│   ├── top.php
│   ├── about.php
│   ├── company.php
│   ├── contact.php
│   └── ...
```

---

## valueファイルの二重構造

各ページの value ファイルは **2つの役割** を持つ：

### 1. 静的データ定義（ローカル表示用ダミーデータ）

`defaultPageValue()` + `setValueTitle()` / `setValueImage()` 等のヘルパーで定義。
PHP ビルトインサーバー（`npm run dev`）での表示に使用。

```php
$page_about = defaultPageValue('about', array(
    'title' => setValueTitle('私たちについて', 'About'),
    'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
    'message' => array(
        'title' => setValueTitle('メッセージ', 'Message'),
        'text'  => 'ダミーテキスト...',
    ),
    'outline' => array(
        'title' => setValueTitle('会社概要', 'Outline'),
        'dl' => array(
            array('th' => '見出し', 'td' => '内容'),
        ),
    ),
));
```

### 2. ACFフィールド定義（WordPress カスタムフィールドの登録）

`registerAcfFromValue()` で静的データから自動生成される。
WordPress 環境で ACF フィールドグループとして自動登録される。

```php
// 推奨: 静的データから自動生成
registerAcfFromValue('privacy', '個人情報保護方針', 'page', $page_privacy);

// CPT の場合: archive + single
registerAcfFromValue($p_key, $p_key . '一覧', 'archive', ${'page_' . $p_key});
registerAcfFromValue($p_key, $p_key . '詳細', 'single', ${$p_key . '_list'}[0], array('title'));
```

#### registerAcfFromValue の自動振り分け

| 静的データのキー    | ACF の配置先                 |
| ------------------- | ---------------------------- |
| `image`, `text`     | 通常フィールドの先頭         |
| `meta`, `thumbnail` | サイドバー（position: side） |
| 上記以外            | 通常フィールド               |

`default_skipkeys`（自動除外）: `nav`, `parent`, `class`, `date`, `id`, `slug`, `post_type`, `section_mode`, `pankuzu`, `taxonomy`

追加で除外したいキーは第5引数で渡す: `registerAcfFromValue(..., array('title'))`

#### 特殊キーの自動検知

`buildAcfField` は以下のキー名を検知し、専用の ACF 構造を返す:

| キー名     | 生成される ACF                             | 静的データの形                                           |
| ---------- | ------------------------------------------ | -------------------------------------------------------- |
| `form`     | group（button_group + lead + text + link） | `'form' => array('type' => 'input', 'lead' => '...')`    |
| `recruit`  | `setAcfRecruit()`                          | `'recruit' => array(...)`                                |
| `relation` | relationship フィールド                    | `'relation' => array('cpt' => 'technology', 'max' => 6)` |

#### flexible_content の動的生成

`body` 等の `acf_fc_layout` を含む配列は、使用されているレイアウトのみを `flexible_content` の選択肢に含める。
`$layoutMap` に登録されているレイアウト: `title`, `text`, `li`, `table3`, `ppcontact`, `sign`, `link`, `links`, `image`, `youtube`, `map`, `textBox`, `note`, `numberList`, `iconList`, `imageBox`, `imageCard`, `imageGallery`, `data`, `chart`

#### repeater + flexible_content パターン

全ての子要素が `title` + `box`（acf_fc_layout 含む配列）を持つ連想配列の場合、自動で `repeater(title + flexible_content)` として生成される。case / technology / service の body セクションで使用。

#### ページ種別ごとの使用パターン

**固定ページ（page）:**

```php
registerAcfFromValue($p_key, '企業情報', 'page', ${'page_' . $p_key});
```

**CPT（archive + single）:**

```php
registerAcfFromValue($p_key, $p_key . '一覧', 'archive', ${'page_' . $p_key});
registerAcfFromValue($p_key, $p_key . '詳細', 'single', ${$p_key . '_list'}[0], array('title'));
```

**フォーム（contact / recruitform）:**

```php
// form キーは自動検知されるので skipKeys から外す。setting は ACF 不要なので除外
registerAcfFromValue($p_key, 'お問い合わせ', 'page', ${'page_' . $p_key}, array('setting'));

// テンプレート共有で confirm/thanks にも ACF を表示する場合:
global $acfvalues;
$templateLocation = array(array(array(
    'param' => 'page_template', 'operator' => '==', 'value' => 'page-contact.php',
)));
if (isset($acfvalues[$p_key])) $acfvalues[$p_key]['location'] = $templateLocation;
```

**関連投稿（relation）:**

```php
// 静的データ側
'relateTechnology' => array(
    'title' => setValueTitle('関連技術', 'Related Technologies'),
    'relation' => array('cpt' => 'technology', 'max' => 6),
),
```

**重要: 手動定義の場合、静的データのキー名と ACF の `name` 引数を一致させること。`registerAcfFromValue` 使用時はキー名一致が自動保証される。**

---

## setAcf ヘルパー関数

ACFフィールド定義でよく使うヘルパー。定義は `_src/_inc/function/index.php` にある。

| 関数               | 用途                     | ACFフィールドタイプ |
| ------------------ | ------------------------ | ------------------- |
| `setAcfTitle()`    | タイトル（fields指定）   | group               |
| `setAcfText()`     | テキスト                 | textarea            |
| `setAcfImage()`    | 画像                     | image               |
| `setAcfLink()`     | リンク                   | group               |
| `setAcfVideo()`    | 動画                     | group               |
| `setAcfTable3()`   | テーブル（dl/table）     | repeater            |
| `setAcfHistory()`  | 沿革                     | repeater            |
| `setAcfMv()`       | メインビジュアル         | group               |
| `setAcfBody()`     | 本文（flexible_content） | flexible_content    |

### 構造系

| ACFタイプ          | 用途                         | 例                     |
| ------------------ | ---------------------------- | ---------------------- |
| `group`            | セクション単位のグルーピング | メッセージセクション   |
| `repeater`         | 繰り返しフィールド           | リスト、沿革           |
| `flexible_content` | 複数ブロックの自由配置       | 記事本文（setAcfBody） |

---

## ページテンプレートでの使用

```php
<?php
// _src/_inc/page/about.php
// $this_page_value にはWP環境ではACFの値、ローカルでは静的データが入る

$sectionValue = $this_page_value['message'];
?>
<section class="page-about__message">
    <?php setHtmlTitle($sectionValue['title'], 'p-title__sec', 'h2'); ?>
    <?php setHtmlText($sectionValue['text'], 'p-text'); ?>
</section>
```

---

## WordPress開発の手順

### 1. sitemap.jsonに追加

```json
{
  "name": "新ページ",
  "slug": "newpage",
  "post_type": "page",
  "check": { "wp": false }
}
```

### 2. valueファイルを作成

`_src/_inc/value/page/newpage.php` に静的データ定義 + `registerAcfFromValue()` を記述。

```php
$p_key = "newpage";
${'page_' . $p_key} = defaultPageValue($p_key, array(
    'title' => setValueTitle('新ページ', 'New Page'),
    'image' => setValueImage(...),
    'text'  => '...',
    'section1' => array(...),
));

registerAcfFromValue($p_key, '新ページ', 'page', ${'page_' . $p_key});
```

### 3. ページテンプレートを作成

`_src/_inc/page/newpage.php` でコンポーネントを配置。

### 4. ビルド・確認

```bash
npm run build
```

ACFフィールドは value ファイルから自動登録されるため、**管理画面での手動設定は不要**。

---

## $acfvalues 定義フロー（Claude Code 向け）

`defaultPageValue` の静的データから `registerAcfFromValue()` で ACF フィールドを自動生成する。

### 基本原則

```
registerAcfFromValue() を使えば、静的データのキー名と ACF の name は自動で一致する。
手動で addAcfValueArray() を書く必要はほとんどない。
```

### Step 1: defaultPageValue を定義する

```php
$p_key = "company";
${'page_' . $p_key} = defaultPageValue($p_key, array(
    'title'    => setValueTitle('企業情報', 'Company'),
    'image'    => setValueImage(...),
    'text'     => '...',
    'mission'  => array(
        'title' => setValueTitle('企業理念', 'Mission'),
        'lead'  => '...',
        'text'  => '...',
    ),
    'overview' => array(
        'title' => setValueTitle('会社概要', 'Overview'),
        'dl'    => array(array('dt' => '会社名', 'dd' => '...')),
    ),
));
```

### Step 2: registerAcfFromValue を呼ぶ

```php
// 固定ページ
registerAcfFromValue($p_key, '企業情報', 'page', ${'page_' . $p_key});

// CPT — archive + single
registerAcfFromValue($p_key, $p_key . '一覧', 'archive', ${'page_' . $p_key});
registerAcfFromValue($p_key, $p_key . '詳細', 'single', ${$p_key . '_list'}[0], array('title'));
```

第5引数で追加の skipKeys を渡せる（例: single で title を WP 標準タイトル欄に任せる場合）。

### 自動判定ルール（buildAcfField）

| 静的データのパターン                                                | ACF マッピング                                                   |
| ------------------------------------------------------------------- | ---------------------------------------------------------------- |
| `setValueTitle(main, sub)` — sub あり                               | `setAcfTitle('title', '見出し', array('main', 'sub'), 'table')` |
| `setValueTitle(main)` — sub なし                                    | `setAcfTitle('title', '見出し', array('main'), 'table')`         |
| `string`                                                            | `textarea`（50文字超 or `<br>`含む → rows:4、それ以外 → rows:1） |
| `setValueImage(...)`                                                | `setAcfImage()`                                                  |
| `setValueLink(...)`                                                 | `setAcfLink()`                                                   |
| `array('cpt'=>...)`                                                 | `setAcfPostList()`                                               |
| `array(array('acf_fc_layout'=>...))`                                | `setAcfBody2()`                                                  |
| `array(array('dt'=>..., 'dd'=>...))` — キー名に career/history 含む | `setAcfHistory()`                                                |
| `array(array('dt'=>..., 'dd'=>...))` — それ以外                     | `setAcfTable3()`                                                 |
| 数値配列（上記以外）                                                | `repeater`（`$value[0]` から sub_fields 推論）                   |
| 連想配列（上記以外）                                                | `group`（再帰的に sub_fields 生成）                              |

### Step 3: 特殊なフィールドが必要な場合のみ手動定義

```php
// conditional_logic 等が必要な場合
$acfvalues[] = addAcfValueArray($p_key, $p_key . '詳細', 'single', array(
    array(
        'type' => 'button_group',
        'name' => 'mode',
        'choices' => array('article' => '記事を書く', 'link' => '外部リンク・PDF'),
    ),
    setAcfLink('link', 'リンク', 'table', array('field' => "", 'operator' => '==', 'value' => 'link')),
    setAcfBody2('body', 'お知らせ', 'block', array('field' => "", 'operator' => '==', 'value' => 'article')),
));
```

### Step 4: 確認チェックリスト

- [ ] `registerAcfFromValue` の第3引数（`'page'` / `'archive'` / `'single'`）がページ種別と一致しているか
- [ ] CPT の場合、archive と single の両方が定義されているか
- [ ] 追加の `skipKeys` が意図通りか（不要なフィールドが ACF に出ていないか）
- [ ] `acf_layout` キーを静的データに追加して layout を制御する場合、テンプレート側に影響がないか

---

## トラブルシューティング

### ACFフィールドがWP管理画面に表示されない

**原因**：

- `registerAcfFromValue()` / `addAcfValueArray()` の `$type` がページ種別と一致していない
- value ファイルが `ja.php` から include されていない
- `test.php` 等で同じキーの `$acfvalues` が上書きされている

**対処**：

- `$type` が `'page'` / `'archive'` / `'single'` のどれか確認
- `_src/_inc/value/ja.php` の include 一覧を確認
- `grep -rn 'acfvalues.*top' _src/` 等でキー名の重複を確認

### WP環境でデータが空になる

**原因**：

- `registerAcfFromValue` 使用時: skipKeys の指定ミスでフィールドが除外されている
- 手動定義時: 静的データのキー名と `setAcf*()` の `name` 引数が不一致
- ACFフィールドに値が入力されていない

**対処**：

- skipKeys を確認
- 手動定義の場合、キー名を照合
- WordPress管理画面で値を入力

### ビルド後にWordPressテーマが反映されない

**原因**：

- ビルドエラー
- キャッシュが残っている

**対処**：

- `npm run build` のエラーを確認
- ブラウザキャッシュをクリア

---

## まとめ

### WordPress開発の流れ

```
sitemap.json に追加
    ↓
value/page/*.php を作成（静的データ + ACFフィールド定義）
    ↓
page/*.php でテンプレート作成
    ↓
ビルド → ACFフィールドは自動登録される
```

### 重要ポイント

1. **sitemap.jsonが基準**：ページ構造はここで管理
2. **valueファイルが二重構造**：静的データ + ACFフィールド定義を1ファイルに持つ
3. **キー名の一致が必須**：静的データとACF定義のキー名がずれるとWP環境で壊れる
4. **ACF管理画面での手動設定は不要**：PHP配列で自動登録される

---

## WordPress フロー

WordPress 環境への移行は、**静的サイト完成後**に以下の流れで行う。

### 実装フェーズ

#### 1️⃣ Docker 初期化スクリプト（wp-cli）で自動設定

Docker コンテナ起動時に、以下を自動実行：

```bash
_wp/init-script.sh
├─ 初期化済みフラグ確認
│  └─ _wp/.wp-initialized が存在 → スキップ
├─ WordPress 基本設定
│  ├─ サイト URL / Home URL
│  ├─ タイムゾーン（Asia/Tokyo）
│  ├─ 日付形式（Y.m.d）
│  └─ 表示設定（1ページあたりの投稿数）
├─ パーマリンク設定
│  └─ カスタム構造（/post-type/%postname%/）
├─ プラグイン有効化
│  └─ Advanced Custom Fields Pro など
├─ 投稿タイプ・タクソノミー登録
│  └─ sitemap.json をパースして wp-cli で自動登録
├─ 固定ページ自動生成
│  └─ sitemap.json の post_type === "page" を抽出して自動作成
└─ 初期化完了フラグ作成
   └─ _wp/.wp-initialized を作成
```

**実行コマンド**:

```bash
docker-compose up
# WordPress コンテナ起動時に自動実行
```

**初期化済みフラグについて**:

`_wp/.wp-initialized` ファイルの**中身**で初期化状態を判定：

- 空ファイル → 未初期化 → init-script.sh を実行
- 中身あり（実行日時） → 初期化済み → スキップ

| シーン           | 操作                                                                                  |
| ---------------- | ------------------------------------------------------------------------------------- |
| 初回セットアップ | `docker-compose up` → 自動実行 → 中身に実行日時が書き込まれる                         |
| 通常起動         | `docker-compose up` → スキップ（中身あり）                                            |
| 再初期化したい   | `> _wp/.wp-initialized && docker-compose restart web_wordpress`<br>（中身を空にする） |

**注意**:

- Docker の bind mount の仕様上、`_wp/.wp-initialized` ファイルは空でも**常に存在**する必要がある
- ファイルを削除すると、Docker が自動でディレクトリ化してしまうため、**中身を空にする**ことで再初期化する

**メリット**:

- 初回のみ実行される（毎回の無駄な処理を削減）
- リポジトリで初期化状態が可視化される
- 中身（実行日時）で最後の初期化タイミングも確認できる

---

#### 2️⃣ 静的サイト完成 → npm run build

\_src/ でのコーディングが完了したら、ビルドを実行：

```bash
npm run build
```

出力内容：

- `_public/` — 静的サイト用 HTML / CSS / JS
- `_wp/themes/assets/` — WordPress テーマ用コンポーネント

**確認事項**:

- [ ] ビルドエラーなし
- [ ] `_public/` に HTML / CSS / JS が出力されているか
- [ ] `_wp/themes/` に PHP ファイルがコピーされているか

---

#### 3️⃣ 入力フェーズ（WordPress管理画面での値入力）

WordPress 管理画面にログイン（http://localhost:9000/wp-admin/）して、以下の順序で値を入力。

**依存関係ベース**の入力順序（タクソノミー → 投稿 → 固定ページ）：

##### **Step 1: 投稿タイプのタクソノミーを入力**

各投稿タイプのカテゴリ / タグを追加：

| 投稿タイプ  | タクソノミー                                 | 入力項目                      |
| ----------- | -------------------------------------------- | ----------------------------- |
| Service     | （なし）                                     | —                             |
| Case        | 技術カテゴリ、テーマ、業界、用途、事例タイプ | 各カテゴリの値を入力          |
| Technology  | カテゴリ、データ種類                         | 各カテゴリの値を入力          |
| Topics      | カテゴリ、タグ                               | カテゴリ1〜4、タグ1〜4 を入力 |
| Publication | （なし）                                     | —                             |
| Recruit     | （なし）                                     | —                             |

**WordPress 操作**:

- `投稿` → `【投稿タイプ名】カテゴリ` → 新規追加
- または `投稿` → `【投稿タイプ名】タグ` → 新規追加

---

##### **Step 2: 投稿タイプの記事を追加**

各投稿タイプの記事を WordPress で作成・入力：

| 投稿タイプ             | 記事数 | 参考資料     |
| ---------------------- | ------ | ------------ |
| Service（事業概要）    | 6件    | テストサイト |
| Case（研究事例）       | 複数   | テストサイト |
| Technology（技術紹介） | 複数   | テストサイト |
| Topics（トピックス）   | 複数   | 既存サイト   |
| Publication（論文）    | 複数   | 既存サイト   |
| Recruit（採用情報）    | 複数   | 既存サイト   |

**WordPress 操作**:

- `投稿` → `【投稿タイプ】` → 新規追加
- タイトル、アイキャッチ、ACFフィールド、タクソノミーを入力
- 下書き保存 → 確認 → 公開

**ACFフィールド確認**:

- [ ] タイトル（main / sub）が正しく入力されているか
- [ ] 画像がアップロードされているか
- [ ] テキストフィールドが正しく表示されているか
- [ ] タクソノミー（カテゴリ / タグ）が正しく選択されているか
- [ ] Flexible Content（body）が正しく構成されているか

---

##### **Step 3: 投稿タイプの一覧ページを入力**

各投稿タイプのアーカイブページ設定：

| 投稿タイプ | ACFフィールド           | 用途                       |
| ---------- | ----------------------- | -------------------------- |
| Service    | title, image, text など | アーカイブページのメタ情報 |
| Case       | title, image, text など | アーカイブページのメタ情報 |
| Technology | title, image, text など | アーカイブページのメタ情報 |
| Topics     | title, image, text など | アーカイブページのメタ情報 |

**WordPress 操作**:

- `投稿` → `【投稿タイプ】アーカイブ` を編集
- ACFフィールド（title, image, text など）を入力
- 公開

---

##### **Step 4: 固定ページ（投稿の引用なし）を入力**

投稿タイプに依存しない固定ページから順に入力：

| ページ               | スラッグ | 参考資料   |
| -------------------- | -------- | ---------- |
| 個人情報保護方針     | privacy  | 既存サイト |
| セキュリティポリシー | security | 既存サイト |

**WordPress 操作**:

- `固定ページ` → 自動生成されたページを編集
- ACFフィールド（title, image, text など）を入力
- 公開

---

##### **Step 5: 固定ページ（投稿の引用あり）を入力**

投稿タイプ（Service / Case / Technology など）を参照する固定ページ：

| ページ   | 引用対象                       | 参考資料     |
| -------- | ------------------------------ | ------------ |
| 企業情報 | なし（セクション）             | 既存サイト   |
| TOP      | Service, Case, Technology など | テストサイト |

**WordPress 操作**:

- `固定ページ` → 自動生成されたページを編集
- ACFフィールド（title, image, text など）を入力
- **関連投稿**（relation）が自動で表示されるか確認
- 公開

**確認事項**:

- [ ] 投稿タイプからのデータ引き込みが正しいか
- [ ] アーカイブページへのリンクが機能しているか
- [ ] フィルター（タクソノミー）が動作しているか

---

#### 4️⃣ フロントエンド確認

WordPress 管理画面での入力完了後、フロントエンドで表示を確認：

**確認項目**:

- [ ] 入力値がフロントエンドに反映されているか
- [ ] 画像が正しく表示されているか
- [ ] レスポンシブ表示（PC / Tablet / SP）に問題ないか
- [ ] リンクが正しく機能しているか
- [ ] タクソノミー（カテゴリ / タグ）フィルターが動作しているか
- [ ] 投稿の関連表示が正しいか

**表示確認方法**:

```bash
# ローカル表示（静的サイト）
npm run dev
# http://localhost:5173

# WordPress 環境
http://localhost:9000
```

---

### 参考：汎用的な入力順序パターン

上記は**依存関係ベース**の推奨順序。別のアプローチもある：

#### パターンB：優先度ベース

重要ページから順に完成させる（TOP → Service → Case → etc）

#### パターンC：セクション構造ベース

ナビゲーション構造（メインナビ → サブナビ）に沿ってグループ化

---

## TODO / 将来実装予定

### 投稿絞り込み filter 機能（Ajax）

- **TODO (2026-04-21 追記)**: 投稿（news / case / works 等のアーカイブ）を絞り込む filter UI を Ajax で動くようにしたい。
- 現状このテンプレートには filter 関連のファイルが 1 つも存在しない。
  - `_src/_inc/function/parts/filter.php` — 未作成
  - `_src/_inc/function/parts/filter-ajax.php` — 未作成
  - `_src/js/modules/FilterController.js` — 未作成（`_src/js/modules/` ディレクトリ自体が無い）
  - `_src/js/modules/FilterHeight.js` — 未作成
  - `_src/scss/global/_blocks.scss` に `.p-filter` 定義無し（`.p-checkbox` のみ既存）
- 実装時は oruche リポジトリから移行する想定。移行時の注意:
  - oruche 側は `b-filter` 命名だが、本プロジェクトは `b-` 接頭語を廃止済みなので **`p-filter` にリネーム**必須
  - oruche 固有テンプレート（`ajax_render_case` / `ajax_render_technology`）は除外
  - CLAUDE.md のコンポーネント一覧には `setHtmlFilter` が既に先行記載されているため、実装後は一覧の整合を確認
- 担当想定: 新規実装につき Sonnet。

---

**最終更新**: 2026年4月21日
