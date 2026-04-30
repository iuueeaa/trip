# template-corporate

Vite + PHP ビルトインサーバー + WordPress で開発するコーポレートサイト用テンプレート。

ローカル開発では2つの確認方法があります。

- **静的 / PHP 確認**: `npm run dev`
- **WordPress 確認**: `docker compose up`

---

## 動作環境

| | バージョン |
|---|---|
| PHP | 8.2.17 |
| Node.js | 20.16.0 |
| npm | 10.2.4 |

```bash
nodebrew install v20.16.0
nodebrew use v20.16.0
```

> **TODO (2026-04-21)**: Node 20.x は 2026-04-30 で EOL（セキュリティパッチ終了）。
> 案件の区切りで **Node 22.x LTS**（2027-04-30 まで Active LTS）への更新を計画。
> 更新時の作業項目:
> - 上記 `動作環境` 表の Node.js バージョンを 22.x に更新
> - 上記 `nodebrew install` コマンドのバージョンを 22.x に更新
> - `package.json` に `engines.node` フィールドを追加（誤バージョン install 防止）
> - `.nvmrc` / `.node-version` ファイル設置の検討（リポジトリ直下）
> - 上げた後、`npm install` 再実行 → `npm run build` / `npm run dev` の動作確認

macOS 2023 以降のモデルでは PHP が未導入の場合があります。`php -v` で確認し、なければ先にインストールしてください。

Windows 環境でも PHP が必要です。PHP ビルトインサーバーを使用するため、事前に PHP をインストールしてください。

---

## クイックスタート

```bash
npm install
npm run dev
```

起動ログに表示される **PHP Server の URL**（`http://192.168.x.x:xxxx`）をブラウザで開いてください。
Vite の URL（5173）を直接開くのではなく、PHP 側の URL が入口です。

---

## ディレクトリ構成

```
_src/          編集元ファイル（日常的に触るのはここ）
_public/       ローカル確認用の出力先（直接編集しない）
_wp/           WordPress 環境一式
docs/          補足ドキュメント
```

### 主な編集対象

| ディレクトリ | 内容 |
|-------------|------|
| `_src/html/` | 静的ページ本体 |
| `_src/_inc/page/` | ページ用 PHP テンプレート |
| `_src/_inc/value/page/` | ページごとのデータ定義・ACF 定義 |
| `_src/scss/` | スタイル |
| `_src/js/` | JavaScript |
| `_src/files/sitemap.json` | サイト構造の定義 |

### 直接編集しないもの

| ディレクトリ | 理由 |
|-------------|------|
| `_public/` | ビルド・watch で自動生成される |
| `_wp/themes/assets/` | `npm run build` 時にコピーされる |

---

## npm run dev の仕組み

`npm run dev` で以下の3プロセスが同時に起動します。

| プロセス | 役割 |
|---------|------|
| PHP ビルトインサーバー | ページ配信（ブラウザはここにアクセス） |
| Vite 開発サーバー | CSS / JS の HMR + PHP 変更時のフルリロード |
| file watchers | `_src` の変更を `_public` に即時コピー |

### データの流れ

```
_src を編集
  |
  +-- Vite: SCSS / JS → HMR でブラウザに即反映
  +-- watch: PHP / files / image → _public にコピー → ViteLiveReload でリロード
  |
_public で確認
```

### ポートについて

- 使用ポートは自動検出し、`.ports.json` に保存されます
- ポートが使用中の場合、自動で別のポートを使います
- PHP の meta.php が `.ports.json` を読み、Vite の接続先を動的に解決します

---

## 開発着手フロー（新規案件）

テンプレートから新しい案件を始めるときの手順です。

### 1. セットアップ

```bash
# リポジトリをクローン or テンプレートからコピー
git clone <url>
cd <project>
npm install
```

### 2. サイト構造を定義

`_src/files/sitemap.json` を案件に合わせて編集し、ページひな型を生成します。

```bash
npm run generate
```

生成されるファイル（既存ファイルはスキップ）:

- `_src/html/[page]/index.php`
- `_src/_inc/page/[page].php`
- `_src/_inc/value/page/[page].php`
- `_src/scss/layout/page/_[page].scss`

### 3. 初期設定を編集

| ファイル | 設定内容 |
|---------|---------|
| `_src/_inc/value/_common.php` | サイト名・企業名・住所・連絡先・API キー・SNS |
| `_src/scss/global/_variable.scss` | カラー・フォント・グリッド・余白 |
| `.env` | `WEBP_ONLY`（画像出力形式）等の環境設定 |

### 4. 開発開始

```bash
npm run dev
```

起動ログに表示される PHP Server の URL をブラウザで開いてください。

---

## 開発の進め方

推奨する実装順序です。前工程が後工程の土台になるため、順番を守ると手戻りが少なくなります。

| 順序 | 作業 | 対象ファイル |
|------|------|-------------|
| 1 | **データ定義** | `_src/_inc/value/page/*.php` |
| 2 | **変数設定** | `_src/scss/global/_variable.scss` |
| 3 | **共通コンポーネント** | `_src/_inc/function/parts/`（p-）→ `blocks/`（b-） |
| 4 | **header / footer** | PHP + SCSS |
| 5 | **ページ実装** | コンポーネント配置 + `_src/scss/layout/page/_*.scss` |
| 6 | **仕上げ** | hover・アニメーション・JS |

---

## 本番用ビルド

```bash
npm run build
```

- `_public/assets/` にビルド済み CSS / JS を出力
- `_public/assets/` を `_wp/themes/assets/` にコピー（WordPress で使えるようになる）
- サーバーデプロイ時は `_public/` フォルダをアップロード

---

## WordPress 開発

### 起動

```bash
docker compose up
```

確認用の URL はコンテナのポート設定に依存します。`docker-compose.yml` で確認してください。

### 初期セットアップ

1. Docker Desktop をインストール
2. `docker compose up` で WordPress を起動
3. All-in-One WP Migration でテンプレートをインポート
4. `sitemap.json` をもとに CPT ができていることを確認
5. 固定ページの作成・テンプレート設定
6. 初期設定の入力
7. `_src/_inc/value/page/*.php` で ACF フィールドを定義

### 注意点

- `npm run dev` のみでは `_wp/themes/assets/` に常時同期されません
- WordPress 側の表示確認には `npm run build` でアセットを反映してください

---

## ページ追加

このテンプレートでは `sitemap.json` がページ構造の基準です。

### 手順

1. `_src/files/sitemap.json` にページを追加
2. `npm run generate` でひな型ファイルを自動生成（sitemap.json を更新した後に実行）
3. `_src/_inc/value/page/*.php` でデータ定義を編集
4. `_src/_inc/page/*.php` でテンプレートを実装
5. `_src/scss/layout/page/_*.scss` でスタイルを追加

### generate が生成するもの

| post_type | 生成ファイル |
|-----------|-------------|
| `page` | html/index.php, page/*.php, value/page/*.php, scss/_*.scss |
| `post` | 上記 + html/detail.php, page/*_archive.php, page/*_single.php |
| `top` / `form` / `link` | 生成しない |

既存ファイルがある場合はスキップします（上書きなし）。

---

## よくある詰まりどころ

### PHP が入っていない

`php -v` で確認。未導入なら先にインストールしてください。

### Vite の URL を開いてしまった

この環境は PHP ページを入口にして、CSS / JS だけ Vite から読み込みます。
起動ログに表示される PHP Server の URL を開いてください。

### ポートが毎回違う

空きポートを自動検出するため変わることがあります。起動ログを確認してください。

### WordPress 側に変更が反映されない

`npm run dev` では `_wp/themes/assets/` へ常時同期されません。
`npm run build` を実行してください。

---

## エラーログの確認

プロジェクトで発生したエラーは以下の場所で確認します。

### WordPress 環境

`wp-content/debug.log` を参照します。`wp-config.php` で `WP_DEBUG` / `WP_DEBUG_LOG` を有効にしてある前提です。

```bash
docker exec <container-name>-web_wordpress-1 cat /var/www/html/wp-content/debug.log
```

コンテナ名は `docker ps` で確認してください。

### 静的 / PHP ビルトインサーバー環境

`npm run dev` を起動したターミナルに PHP エラーが stderr として出力されます。Vite / Node スクリプトのエラーも同じターミナルで確認できます。

---

## 変更履歴

ソースコードの主要な変更を記録します。

### 2025-09-17

- **_common.php**: `$snslist` に `text` フィールドを追加
- **_variable.scss**:
  - `var(--Gap)` を廃止
  - 色変数を変更（反転時に反転させない `BasicColor` と、反転していなくても使える `InvertColor` を追加）
  - `--fzs` を px に変更（最小文字サイズとしてわかりやすく）
  - コンテンツ幅を変数化: `--ml` / `--mr` → `--contentLeft` / `--contentRight` / `--commonWidth`
  - グリッドシステムを導入
  - 共通サイズ設定を変数化
- **svg.php**: クラス引数を追加 — `setHtmlSvg('icon-name')` → `setHtmlSvg('icon-name', 'class')`
- **logo.php**: テキスト引数を追加 — `setHtmlLogo('icon-name', 'class')` → `setHtmlLogo('icon-name', 'class', 'text')`
- **_l-header.php**: 更新
- **_header.scss** / **_footer.scss**: 初期スタイル削除

---

## 関連ドキュメント

| ファイル | 内容 |
|---------|------|
| [CLAUDE.md](CLAUDE.md) | AI 運用ルール・@fix フロー |
| [docs/repository_rules.md](docs/repository_rules.md) | コーディング規約・SCSS ルール・ページ構造 |
| [docs/wp-structure.md](docs/wp-structure.md) | WordPress 構造（CPT・ACF） |
