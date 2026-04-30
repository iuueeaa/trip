# システム開発プロジェクト固有ルール

このファイルは CLAUDE.md の共通ルールを補完する。
オリジナルシステム(Vue + Laravel 等)、WordPress プラグイン、
Shopify アプリ等のプロジェクトで適用。

---

## プロジェクト種別

- オリジナルシステム(Vue + Laravel / Next.js + Express 等)
- WordPress プラグイン
- Shopify アプリ
- その他

実プロジェクトでは該当する種別のみ残し、それ以外を削除して使用。

---

## ファイル構造

### Vue + Laravel (例)

```
app/                   # Laravel バックエンド
├── Http/Controllers/
├── Models/
└── Services/
resources/
├── js/                # Vue フロントエンド
│   ├── components/
│   ├── pages/
│   └── stores/
└── views/             # Blade テンプレート
routes/
├── api.php
└── web.php
database/
├── migrations/
└── seeders/
```

### WordPress プラグイン (例)

```
plugin-name/
├── plugin-name.php    # メインファイル
├── includes/          # クラス定義
├── admin/             # 管理画面
├── public/            # フロント
└── languages/         # 翻訳
```

### Shopify アプリ (例)

```
app/                   # Remix(Shopify CLI 標準)
├── routes/
├── shopify.server.ts
└── components/
extensions/            # アプリ拡張
prisma/                # DB スキーマ
```

実プロジェクト構成に合わせて書き換える。

---

## 仕様の正本

実装・修正時に参照する場所をプロジェクト構成に合わせて記載:

- API 仕様: (例: `docs/api.md` / OpenAPI 定義 / Swagger)
- DB スキーマ: (例: `database/migrations/` / `prisma/schema.prisma`)
- コンポーネント定義: (例: `resources/js/components/` / Storybook)
- 環境変数: (例: `.env.example`)
- 認証/権限: (例: `app/Policies/` / Middleware)

---

## テスト駆動の使い分け

システム開発はロジックが厚く目視では判定しきれないため、
コア領域はテスト先行で進める。WEB制作とは方針が異なる。

### テスト先行が必要な場面

以下はテストコードを先に書き、それを通すまで実装をループする:

- 認証・権限処理(誰が何にアクセスできるか)
- 計算ロジック(料金・在庫・スコア・ランク判定等)
- DB 操作の整合性(トランザクション・外部キー・カスケード)
- バグ修正(再現テストを先に書く → 修正 → テストが通る)
- リファクタリング(変更前後でテスト緑を維持)

### テスト不要 / 動作確認で十分な場面

- CRUD の単純な画面表示
- スタイル調整
- プロトタイプ段階の試行錯誤
- 設定値の変更

### 受け入れ条件のテストコード化

TASK 起票時の「受け入れ条件」をテストコードに変換できる場合、
そのテストを最初に書いてから実装する。

例:
```
受け入れ条件: 権限なしユーザーは 403 を返す
  ↓
test('returns 403 for unauthorized user', ...) を先に書く
  ↓
このテストが通る最小実装をする
```

### AI への指示

テスト先行が必要な領域では:
1. ユーザーの要求からテストケースを列挙
2. テストコードを書く(失敗する状態でコミット可)
3. テストを通す最小実装をする
4. 全テスト緑を確認してから完了報告

「動きました」だけの報告は無効。テスト緑の証拠が必須。

---

## 命名規則

プロジェクトの慣習に合わせて記載:

- 関数: `camelCase` / `snake_case`
- クラス: `PascalCase`
- コンポーネント: `PascalCase.vue` / `PascalCase.tsx`
- DB テーブル: `snake_case` 複数形
- API エンドポイント: `/api/v1/resource-name` (kebab-case)

---

## デバッグ

該当環境のデバッグ手段を記載:

- ログ出力先(例: `storage/logs/laravel.log`)
- DB 確認コマンド(例: `php artisan tinker`)
- フロント DevTools(Vue / React Devtools)
- API 確認(例: Postman / curl 例)

---

## プロジェクト固有プロンプト

### 新規 API 追加

```
「○○」APIを追加したいです。
1. ルート定義(routes/api.php 等)
2. Controller / Action 作成
3. リクエスト/レスポンスの型定義
4. 認証・認可の確認
```

### 新規コンポーネント追加

```
「○○」コンポーネントを追加したいです。
1. 配置先ディレクトリ
2. Props / Emits の仕様
3. 既存コンポーネントとの共通化検討
```

### マイグレーション

```
「○○」テーブルを追加/変更したいです。
1. マイグレーションファイル作成
2. 既存データへの影響
3. ロールバック手順
4. 関連するモデル/型定義の更新
```
