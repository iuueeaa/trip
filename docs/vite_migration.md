# Gulp -> Vite 移行指示書（Claude Code用 / _src確認後の更新版）

## 目的
既存の Gulp ベース開発環境を、提示済みの Vite ベース開発環境へ移行する。

この移行は単純なビルドツール置換ではなく、以下の既存運用を維持する前提で行うこと。

- `_src` を編集元にする
- `_public` を静的/PHP確認用の出力先にする
- `_wp/themes/assets` に assets を同期する
- PHPテンプレートの include 構造を維持する
- 画像圧縮、SVGスプライト、`html/inc/files` のコピーを維持する
- 既存の asset 参照パスを極力壊さない

---

## 既存 Gulp 環境の確定事項

### package.json
- `install` 時に `npx gulp init` が実行される。fileciteturn0file3
- 開発コマンドは `gulp`, `server`, `browsersync`, `start`。fileciteturn0file3
- browserslist は `last 2 version`, `> 5%`, `ie >= 11`。fileciteturn0file3

### gulpfile.js
- `css`: `_src/scss/**/*.scss` をコンパイルし、`_public/assets/css` と `_wp/themes/assets/css` に出力。fileciteturn0file1
- `js`: 現状は webpack 実行ではなく、`_src/js/**/*.js` をそのまま `_public/assets/js` と `_wp/themes/assets/js` にコピー。fileciteturn0file1
- `html`: `_src/html/**/*.php` を `_public` にコピー。`_src/html/_inc/**/*.php` は除外。fileciteturn0file1
- `inc`: `_src/html/_inc/**/*.php` を `_public/assets/inc` と `_wp/themes/assets/inc` にコピー。fileciteturn0file1
- `file`: `_src/files/**/*` を `_public/assets/files` と `_wp/themes/assets/files` にコピー。fileciteturn0file1
- `image`: `_src/image/**/*` を圧縮し、`_public/assets/image` と `_wp/themes/assets/image` に出力。`svg-sprite` 配下は除外。fileciteturn0file1
- `svg`: `_src/image/svg-sprite/**/*.svg` から sprite を生成。fileciteturn0file1
- `sync` / `sync_docker`: PHP サーバ + BrowserSync。`.env` の `PORT` / `ROOTPATH` 依存。fileciteturn0file1

### webpack.config.js
- entry は `./_src/js/index.js`。fileciteturn0file5
- ただし gulp 側では webpack 実行がコメントアウトされ、現在は未使用。fileciteturn0file1turn0file5

### docker-compose.yml
- frontend は `npx gulp docker` 前提。現状のままでは Vite 環境に合わない。fileciteturn0file0

---

## _src 確認で判明した重要事項

### 1. JS は「モジュール化前提」ではなく「そのまま配信前提」に近い
確認できた主なファイル:

- `_src/js/index.js`
- `_src/js/wpadmin.js`
- `_src/js/splide.min.js`
- `_src/js/splide-extension-auto-scroll.min.js`
- `_src/js/plugin/jquery.tile.min.js`

`_src/js/index.js` は大規模なプレーン JS で、`import` / `export` は使われていない。グローバル関数・グローバル変数前提の実装として扱うこと。

### 2. jQuery 依存は「全面依存ではない」が残存している
- `plugin/jquery.tile.min.js` が存在する
- `index_bk（クラス化前）.js` に jQuery の痕跡がある
- ただし現行 `index.js` は少なくとも先頭確認範囲ではプレーン JS ベース

方針:
- **まずは jQuery を即削除しないこと**
- 実行系で `jquery.tile.min.js` が読み込まれているかを確認し、必要なら Vite でも配信対象として残す
- 将来的な削除は別タスクに分離する

### 3. Sass 構成はすでに `@use` ベース
確認できた構成:

- `style.scss`
- `wpadmin.scss`
- `global/_index.scss`
- `layout/_index.scss`
- `dev/_index.scss`
- 多数の page partial

`style.scss` は `@use "global"; @use "layout"; @use "dev";` の構成。これは Vite へ比較的そのまま移しやすい。

### 4. PHP 側の asset 参照は固定パス前提
確認できた主要参照:

- CSS: `<?php echo $rocal_path; ?>/assets/css/style.css`
- JS: `<?php echo $rocal_path; ?>/assets/js/index.js`
- 追加JS: `splide.min.js`, `splide-extension-auto-scroll.min.js`
- include: `assets/inc/_l-head.php`, `assets/inc/_l-page.php`, `assets/inc/_l-foot.php`
- JSON: `assets/files/sitemap.json`
- SVG sprite: `assets/image/icon/sprite.svg`

したがって、**manifest ベース差し替えよりも、固定ファイル名出力を維持する方が安全**。

### 5. `_src/html/_inc` は実質的にアプリ本体
確認できた構成:

- `_inc/_l-head.php`
- `_inc/_l-header.php`
- `_inc/_l-foot.php`
- `_inc/_l-page.php`
- `_inc/function/*.php`
- `_inc/value/*.php`
- `_inc/page/*.php`

このため、`_src/html -> _public` のコピーだけでは不十分。`_inc` コピーは必須。

### 6. SVG素材の配置ルールは旧Gulpと新Viteで差異がある
- 旧Gulp: `_src/image/svg-sprite/**/*.svg`
- 新Vite案: `_src/icon/*.svg` 系の可能性あり

方針:
- **今回の移行では素材置き場を変更しない**
- まずは `_src/image/svg-sprite/**/*.svg` を入力として扱えるよう Vite 側スクリプトを合わせる
- フォルダ変更は別フェーズに分離する

---

## 移行の結論

### 結論1
**今回の移行では、Vite は「JS/CSSビルド担当」、HTML/PHP/INC/files/image/svg/wp同期は「周辺スクリプト担当」として使う。**

### 結論2
**JS はいきなり完全なES Modules化をしない。**
まずは既存の `index.js` をエントリとしてそのまま安定出力し、必要なライブラリファイルは静的コピーまたは明示 import で扱う。

### 結論3
**PHP 側の読み込みパスは固定のまま維持する。**
つまり出力ファイル名は少なくとも以下を維持すること。

- `assets/css/style.css`
- `assets/js/index.js`
- `assets/js/splide.min.js`
- `assets/js/splide-extension-auto-scroll.min.js`
- `assets/inc/**`
- `assets/files/**`
- `assets/image/**`

---

## Claude Code への実装指示

以下を順番に実行すること。

### Phase 1: Vite構成を既存運用に寄せる
1. Vite の build 出力先は `_public/assets` を維持する。
2. CSS 出力は必ず `css/style.css` に固定する。
3. JS 出力は必ず `js/index.js` に固定する。
4. manifest は生成してもよいが、PHP 側では使わない前提で進める。
5. 開発時も本番時も、既存の PHP テンプレートが asset 名を変更せず参照できる状態にする。

### Phase 2: JS エントリの整理
1. 旧 `./_src/js/index.js` を、Vite 側の正式な entry にする。
2. `./_src/main.js` 前提なら、その中で `import './js/index.js'` とするか、entry 自体を `./_src/js/index.js` に変更する。
3. `splide.min.js` と `splide-extension-auto-scroll.min.js` は以下どちらかで扱う。
   - 既存どおり `assets/js` に静的コピーする
   - npm パッケージに置き換えて `index.js` から import する
4. 今回は安全性優先のため、**まずは静的コピーを優先**する。
5. `wpadmin.js` は現行で使用ページが不明なため、削除しない。必要に応じて別エントリまたは静的コピーとして残す。
6. `plugin/jquery.tile.min.js` は、現行 `index.js` で必要か検索し、必要なら静的コピー対象に残す。

### Phase 3: Sass 移行
1. `./_src/scss/style.scss` を正式エントリにする。
2. `@use` 構成はそのまま維持する。
3. 既存 CSS 出力名 `style.css` を変えない。
4. `wpadmin.scss` は利用箇所を検索し、必要なら別エントリにする。不要なら保留でよい。

### Phase 4: コピー系処理の移行
以下を Vite 以外の補助スクリプトで実現する。

1. `_src/html/**/*.php` → `_public/**`
   - ただし `_src/html/_inc/**` は除外
2. `_src/html/_inc/**/*.php` → `_public/assets/inc/**`
3. `_src/files/**/*` → `_public/assets/files/**`
4. `_public/assets/**/*` → `_wp/themes/assets/**`

差分コピーでよいが、初回 build では全件出力すること。

### Phase 5: 画像処理
1. `_src/image/**/*` を処理し、`_public/assets/image` に出力する。
2. `svg-sprite` と `svg-sprite-bg` は通常画像処理から除外する。
3. 出力後に `_wp/themes/assets/image` へ同期する。

### Phase 6: SVGスプライト
1. 入力は **旧運用どおり** `_src/image/svg-sprite/**/*.svg` を使う。
2. 出力は `assets/image/icon/sprite.svg` を維持する。
3. PHP 側で `assets/image/icon/sprite.svg` を直接参照しているため、ここは変えない。

### Phase 7: PHP / BrowserSync / Live Reload
1. `_public` をドキュメントルートにして PHP サーバを起動する。
2. BrowserSync は PHP サーバに proxy する。
3. 以下の変更でリロードされるようにする。
   - `_public/**/*.php`
   - `_public/assets/inc/**/*.php`
   - `_public/assets/css/**/*.css`
   - `_public/assets/js/**/*.js`
4. 旧 `.env` の `ROOTPATH` 前提が残る場合は、新環境でも読み込めるよう維持する。

### Phase 8: Docker 対応
1. 旧 `docker-compose.yml` の frontend command は `npx gulp docker` なので置換が必要。fileciteturn0file0
2. frontend の command は Vite 用に変更する。
3. まずは以下のいずれかで動作確認する。
   - `npm install && npm run dev`
   - `npm install && npm run build && npm run watch`
4. Docker ではポート解決・BrowserSync・PHP proxy が複雑になるため、**先にローカルで移行完了させ、その後 Docker を合わせる**こと。

---

## 廃止対象
以下は移行完了後に削除候補。

- `gulpfile.js`
- `webpack.config.js`
- gulp 系 devDependencies
- `node-env-file`
- `webpack`, `webpack-cli`, `webpack-stream`

ただし、**完全移行確認前に削除しないこと**。

---

## Claude Code に渡す実行プロンプト

以下をそのまま実行指示として使うこと。

```md
このプロジェクトを、既存運用を壊さずに Gulp から Vite へ移行してください。

制約:
- `_src` を編集元に維持
- `_public` を確認環境に維持
- `_wp/themes/assets` への同期を維持
- PHP の include 構造を壊さない
- 既存の asset 参照パスを極力変えない
- 出力ファイル名は `assets/css/style.css` と `assets/js/index.js` を維持
- `_src/html/_inc` は `assets/inc` へコピー維持
- `_src/files` は `assets/files` へコピー維持
- 画像処理と SVG sprite を維持
- SVG sprite の入力は `_src/image/svg-sprite/**/*.svg`、出力は `assets/image/icon/sprite.svg` を維持
- JS はいきなり全面 ES Modules 化しない
- まずは既存 `./_src/js/index.js` を安全に Vite エントリへ載せる
- `splide.min.js` と `splide-extension-auto-scroll.min.js` は初回は静的コピーでよい
- jQuery 関連は必要性を確認し、不要確定までは削除しない

やること:
1. 既存 `_src` と Vite 雛形を比較
2. Vite 設定を既存パス運用に合わせて修正
3. JS/CSS エントリを調整
4. html/inc/files/image/svg/wpassets の補助スクリプトを整備
5. dev/build/watch が通る状態にする
6. 旧 Gulp との差分と、残課題を README または migration note にまとめる

最終成果物:
- 動作する `package.json`
- 動作する `vite.config.*`
- 必要な補助スクリプト
- 変更されたディレクトリ構成
- 移行メモ
```

---

## 検証項目

- `npm install` が通る
- `npm run dev` で `_public` ベースの確認ができる
- `style.css` が出力される
- `index.js` が出力される
- `assets/inc` の include が壊れない
- `assets/files/sitemap.json` が参照できる
- `assets/image/icon/sprite.svg` が生成される
- `_wp/themes/assets` に同期される
- 旧主要ページ (`index`, `about`, `news`, `item`, `topics`, `brand`, `look`) が表示崩れなく開ける

