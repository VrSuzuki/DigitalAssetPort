# DigitalAssetPort

![Laravel](https://img.shields.io/badge/Laravel-8.x-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white)
![Fortify](https://img.shields.io/badge/Auth-Laravel%20Fortify-FF2D20)
![Google OAuth](https://img.shields.io/badge/OAuth-Google-4285F4?logo=google&logoColor=white)
![Stripe](https://img.shields.io/badge/Payment-Stripe-635BFF?logo=stripe&logoColor=white)
![MailHog](https://img.shields.io/badge/Mail-MailHog-29B6F6)
![Ubuntu](https://img.shields.io/badge/Ubuntu%20%2F%20WSL2-E95420?logo=ubuntu&logoColor=white)
![Frontend](https://img.shields.io/badge/Frontend-HTML%20%2F%20CSS%20%2F%20JavaScript-F7DF1E?logo=javascript&logoColor=111)

DigitalAssetPort は、テンプレート、教材、コード、画像、音声、動画、3Dモデルなど様々なデジタルデータを販売・配布できるローカル完結型のフリマECサイトです。
 
個人的なLaravelの技術検証目的で作成しました。  
こちらで培った技術をもとに、将来的には※動場(どうば)共有プラットフォームを構築する足掛かりにしていきたいです。

※動場：動きのある3D空間に対して私が呼んでいる造語です。
  
  
## 背景
2024年頃にLaravelの学習用途でフリマWebアプリを作成したのですが、粗削りな部分もあったため、Laravelの復習も兼ねて0から作り直し、公開できるような形にまで改善しました。

## スクリーンショット
[画面のスクリーンショット一覧はこちら](/ScreenShotTable.md)
<img width="1599" height="2404" alt="1_トップページ" src="https://github.com/user-attachments/assets/5596b223-017d-40f4-a028-d644a1ffdc92" />

## 動作風景
YouTube動画のリンク
[!['DigitalAssetPort紹介動画'](https://github.com/user-attachments/assets/2f05bf95-8806-4d5c-9165-cedc3e62b3f5)](https://www.youtube.com/watch?v=vCpvSRH-AgY)

  
## 主な機能

- Laravel Fortify による会員登録、ログイン、ログアウト、メール認証、パスワード再設定
- Google OAuth によるGoogleアカウント登録・ログイン
- Google OAuthユーザーはメール認証をスキップし、認証後すぐプロフィール登録へ遷移
- メール登録後は認証待機画面へ遷移し、メール認証完了後にプロフィール編集へ進む導線
- 日本語バリデーションメッセージ、フォーム属性名の日本語化
- コンテンツ投稿、編集、一覧、詳細、タグ、30ジャンル、180サブジャンル、表示順、表示件数、詳細検索
- ジャンル選択後にサブジャンルを絞り込む投稿・検索フォーム
- トップページのジャンル、フォロー中、投稿者一覧の上位表示と「もっと表示する」展開
- 複数紹介画像、詳細画面ギャラリー、サムネイルクリック、左右ボタンによる画像切り替え
- 投稿済みコンテンツ画像の削除、画像追加時のクロップ、追加直後の画像削除
- プロフィールアイコンの正方形クロップ、コンテンツ画像の自由比率クロップ
- SVGアイコンを切り替えるお気に入り、コメント、購入者限定レビュー、フォロー、フォロワーリスト、省略表示付き通知ドロップダウン
- カート、Stripe Checkout、Stripe未設定時のローカル決済フォールバック
- 購入履歴、購入詳細、ライブラリ、ダウンロード用サンプルレスポンス
- 売上管理、月別売上、日別売上、コンテンツ別売上、注文確認
- アカウント設定、通知表示設定、フォロー数表示設定、アカウント削除
- レスポンシブ対応、Material Symbolsアイコン、ローカルSVGアバター、カスタムページネーション
- Seederによるデモユーザー、コンテンツ、購入、レビュー、通知、お気に入り、フォローの再生成

## ローカルでの本Webアプリの構築・起動
### 前提条件
本Webアプリを構築・起動するには以下が必要です。
- Docker Desktop
- Docker Compose
- WSL2 + Ubuntu（※Windows環境の場合）

### 構築・起動する手順
pullした「DigitalAssetPort」ディレクトリ直下で以下のコマンドを実行することで、ローカルで動くWebアプリが作成・起動されます。
```bash
docker compose up -d --build
docker compose exec php composer install
cp src/.env.example src/.env
docker compose exec php php artisan key:generate
docker compose exec php php artisan storage:link
docker compose exec php php artisan migrate:fresh --seed
```
もしもブラウザ(`http://localhost/`)にて権限に関するエラーが表示されていた場合は以下のコマンドを「DisitalAssetPort」ディレクトリ直下で実行してみてください。

```
docker compose exec php chmod -R 775 storage bootstrap/cache
docker compose exec php chown -R www-data:www-data storage bootstrap/cache
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

Seeder は既存の会員・コンテンツ系データを削除し、12ユーザー、30ジャンル、180サブジャンル、60コンテンツ、複数紹介画像、購入履歴、レビュー、通知、フォロー、お気に入りを作成します。

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

## Google認証

Google Cloud Console でOAuthクライアントを作成し、承認済みリダイレクトURIに `http://localhost/auth/google/callback` を登録してください。
その後、`src/.env` に以下を設定するとGoogleアカウント登録・ログインが使えます。
Google認証で作成・連携したユーザーはGoogle側でメールアドレス確認済みとして扱い、MailHogでの追加メール認証なしにプロフィール登録へ進みます。

```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback
```

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
        string google_id UK
        text google_avatar_url
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
