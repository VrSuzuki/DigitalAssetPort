# DigitalAssetPort

![Laravel](https://img.shields.io/badge/Laravel-8.x-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white)
![Fortify](https://img.shields.io/badge/Auth-Laravel%20Fortify-FF2D20)
![Stripe](https://img.shields.io/badge/Payment-Stripe-635BFF?logo=stripe&logoColor=white)
![MailHog](https://img.shields.io/badge/Mail-MailHog-29B6F6)
![Frontend](https://img.shields.io/badge/Frontend-HTML%20%2F%20CSS%20%2F%20JavaScript-F7DF1E?logo=javascript&logoColor=111)

DigitalAssetPort は、テンプレート、教材、コード、画像、音声、動画、3Dモデルなどのデジタルデータを販売・配布できるローカル完結型のフリマECサイトです。

## スクリーンショット
<img width="1599" height="2404" alt="1_トップページ" src="https://github.com/user-attachments/assets/5596b223-017d-40f4-a028-d644a1ffdc92" />
[画面一覧はこちら](main/ScreanShotTable.md)

## 主な機能

- Laravel Fortify による会員登録、ログイン、ログアウト、メール認証、パスワード再設定
- 日本語バリデーションメッセージ、フォーム属性名の日本語化
- コンテンツ投稿、編集、一覧、詳細、タグ、ジャンル、サブジャンル、表示順、表示件数、詳細検索
- 複数紹介画像、詳細画面ギャラリー、サムネイルクリック、左右ボタンによる画像切り替え
- プロフィールアイコンの正方形クロップ、コンテンツ画像の自由比率クロップ
- お気に入り、コメント、購入者限定レビュー、フォロー、フォロワーリスト、通知
- カート、Stripe Checkout、Stripe未設定時のローカル決済フォールバック
- 購入履歴、購入詳細、ライブラリ、ダウンロード用サンプルレスポンス
- 売上管理、月別売上、日別売上、コンテンツ別売上、注文確認
- アカウント設定、通知表示設定、フォロー数表示設定、アカウント削除
- レスポンシブ対応、Material Symbolsアイコン、ローカルSVGアバター、カスタムページネーション
- Seederによるデモユーザー、コンテンツ、購入、レビュー、通知、お気に入り、フォローの再生成

## ローカル起動

```bash
docker compose up -d --build
docker compose exec php composer install
docker compose exec php php artisan storage:link
docker compose exec php php artisan migrate:fresh --seed
```

`.env` がない場合は `src/.env.example` を `src/.env` にコピーし、次を実行してください。

```bash
docker compose exec php php artisan key:generate
```

## サンプルアカウント

全アカウントのパスワードは `password` です。

| 用途 | メールアドレス |
| --- | --- |
| 管理・動作確認 | `admin@example.com` |
| ビジネス系投稿者 | `office@example.com` |
| 開発教材投稿者 | `code@example.com` |
| 学習教材投稿者 | `study@example.com` |
| 生活系投稿者 | `life@example.com` |
| 製造系投稿者 | `factory@example.com` |
| クリエイティブ投稿者 | `creative@example.com` |

Seeder は既存の会員・コンテンツ系データを削除し、12ユーザー、36コンテンツ、複数紹介画像、購入履歴、レビュー、通知、フォロー、お気に入りを作成します。

## URL一覧

| 画面 | URL |
| --- | --- |
| トップ / コンテンツ一覧 | `http://localhost/` |
| サービス紹介 | `http://localhost/about` |
| 詳細検索 | `http://localhost/search` |
| コンテンツ詳細 | `http://localhost/contents/{slug}` |
| コンテンツ投稿 | `http://localhost/contents/create` |
| ログイン | `http://localhost/login` |
| アカウント登録 | `http://localhost/register` |
| プロフィール編集 | `http://localhost/profile/edit` |
| ユーザープロフィール | `http://localhost/users/{handle}` |
| フォローリスト | `http://localhost/users/{handle}/following` |
| フォロワーリスト | `http://localhost/users/{handle}/followers` |
| お気に入り | `http://localhost/favorites` |
| カート | `http://localhost/cart` |
| ライブラリ | `http://localhost/library` |
| 購入履歴 | `http://localhost/purchases` |
| 売上管理 | `http://localhost/sales` |
| 通知一覧 | `http://localhost/notifications` |
| アカウント設定 | `http://localhost/settings` |
| MailHog | `http://localhost:8025` |
| phpMyAdmin | `http://localhost:8080` |

## 決済

`.env` に `STRIPE_KEY` と `STRIPE_SECRET` を設定すると Stripe Checkout に遷移します。
決済時のカード番号はテスト番号 `4242 4242 4242 4242` を入れて、それ以外は適当に記入し、決済を行うと支払い決済完了となります。

## ディレクトリ構成

```text
.
├── docker/                 # nginx / php / mysql 設定
├── docker-compose.yml
└── src/
    ├── app/                # Models, Controllers, Fortify Actions
    ├── database/           # Migrations, Seeders, Factories
    ├── public/             # 公開CSS/JS、SVGアセット
    ├── resources/          # Blade、CSS/JSソース、lang
    └── routes/web.php
```

## ER図

```mermaid
erDiagram
    USERS ||--o{ CONTENTS : posts
    USERS ||--o{ COMMENTS : writes
    USERS ||--o{ FAVORITES : marks
    USERS ||--o{ CARTS : owns
    USERS ||--o{ ORDERS : places
    USERS ||--o{ LIBRARY_ITEMS : stores
    USERS ||--o{ APP_NOTIFICATIONS : receives
    USERS ||--o{ FOLLOWS : follower
    USERS ||--o{ FOLLOWS : following

    GENRES ||--o{ SUB_GENRES : contains
    GENRES ||--o{ CONTENTS : classifies
    SUB_GENRES ||--o{ CONTENTS : narrows

    CONTENTS ||--o{ CONTENT_IMAGES : shows
    CONTENTS ||--o{ COMMENTS : has
    CONTENTS ||--o{ FAVORITES : has
    CONTENTS ||--o{ CART_ITEMS : added
    CONTENTS ||--o{ ORDER_ITEMS : sold
    CONTENTS ||--o{ LIBRARY_ITEMS : unlocked
    CONTENTS }o--o{ TAGS : tagged

    CARTS ||--o{ CART_ITEMS : includes
    ORDERS ||--o{ ORDER_ITEMS : includes
    ORDER_ITEMS ||--o| LIBRARY_ITEMS : unlocks

    USERS {
        bigint id PK
        string handle UK
        string name
        string nickname
        string email UK
        string password
        string avatar_path
        text bio
        boolean notifications_enabled
        boolean show_following_count
        boolean show_follower_count
        timestamp email_verified_at
    }

    GENRES {
        bigint id PK
        string name
        string slug UK
        text description
    }

    SUB_GENRES {
        bigint id PK
        bigint genre_id FK
        string name
        string slug UK
    }

    CONTENTS {
        bigint id PK
        bigint user_id FK
        bigint genre_id FK
        bigint sub_genre_id FK
        string title
        string slug UK
        string format
        text description
        int price
        string thumbnail_path
        string file_path
        string license_type
        string environment
        decimal file_size_mb
        int rating_rate
        int ratings_count
        int profile_order
        string status
        timestamp published_at
    }

    CONTENT_IMAGES {
        bigint id PK
        bigint content_id FK
        string path
        tinyint sort_order
    }

    TAGS {
        bigint id PK
        string name UK
        string slug UK
    }

    FAVORITES {
        bigint id PK
        bigint user_id FK
        bigint content_id FK
    }

    FOLLOWS {
        bigint id PK
        bigint follower_id FK
        bigint following_id FK
    }

    COMMENTS {
        bigint id PK
        bigint user_id FK
        bigint content_id FK
        text message
        boolean is_recommended
    }

    CARTS {
        bigint id PK
        bigint user_id FK
        boolean active
    }

    CART_ITEMS {
        bigint id PK
        bigint cart_id FK
        bigint content_id FK
    }

    ORDERS {
        bigint id PK
        bigint user_id FK
        string order_number UK
        int total_amount
        string stripe_session_id
        string status
        timestamp purchased_at
    }

    ORDER_ITEMS {
        bigint id PK
        bigint order_id FK
        bigint content_id FK
        int price
    }

    LIBRARY_ITEMS {
        bigint id PK
        bigint user_id FK
        bigint content_id FK
        bigint order_item_id FK
        string added_type
    }

    APP_NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        bigint actor_id FK
        string type
        string message
        string url
        timestamp read_at
    }
```
