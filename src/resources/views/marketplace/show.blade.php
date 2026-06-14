@extends('layouts.base')

@section('title', $content->title.' | DigitalAssetPort')

@section('body')
  @include('layouts.header')

  <main class="app-main">
    @include('partials.flash')
    @include('partials.errors')

    <div class="detail-layout">
      <article>
        @php
          $galleryImages = $content->images->count()
              ? $content->images->map->url
              : collect([$content->thumbnail_url]);
        @endphp
        <div class="detail-gallery" data-gallery>
          <div class="detail-image">
            <img src="{{ $galleryImages->first() }}" alt="{{ $content->title }}" data-gallery-main>
            @if($galleryImages->count() > 1)
              <button class="gallery-arrow gallery-arrow--prev" type="button" data-gallery-prev aria-label="前の画像">
                <span class="material-symbols-outlined" aria-hidden="true">chevron_left</span>
              </button>
              <button class="gallery-arrow gallery-arrow--next" type="button" data-gallery-next aria-label="次の画像">
                <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
              </button>
            @endif
          </div>
          @if($galleryImages->count() > 1)
            <div class="image-strip">
              @foreach($galleryImages as $image)
                <button class="image-strip__item {{ $loop->first ? 'is-active' : '' }}" type="button" data-gallery-thumb data-gallery-index="{{ $loop->index }}" aria-label="画像{{ $loop->iteration }}を表示">
                  <img src="{{ $image }}" alt="{{ $content->title }} 画像{{ $loop->iteration }}">
                </button>
              @endforeach
            </div>
          @endif
        </div>

        <section class="panel" style="margin-top: 16px;">
          <div class="pill-row">
            <span class="pill">{{ $content->genre->name }}</span>
            <span class="pill pill--coral">{{ $content->subGenre->name }}</span>
          </div>
          <h1 class="section-title" style="margin-top: 12px;">{{ $content->title }}</h1>
          <p style="margin-top: 16px; white-space: pre-wrap;">{{ $content->description }}</p>

          <div class="pill-row" style="margin-top: 18px;">
            @foreach($content->tags as $tag)
              <span class="pill">{{ $tag->name }}</span>
            @endforeach
          </div>
        </section>

        <section class="panel" style="margin-top: 16px;" id="comments">
          <h2>コメント</h2>
          @auth
            @if($hasPurchased && $content->user_id !== auth()->id())
              <form action="{{ route('comments.store', $content) }}" method="POST" style="margin-bottom: 14px;">
                @csrf
                <fieldset class="review-choice">
                  <legend>このコンテンツをおすすめしますか？</legend>
                  <label>
                    <input type="radio" name="is_recommended" value="1" {{ old('is_recommended', '1') === '1' ? 'checked' : '' }}>
                    <span class="material-symbols-outlined" aria-hidden="true">thumb_up</span>
                    はい
                  </label>
                  <label>
                    <input type="radio" name="is_recommended" value="0" {{ old('is_recommended') === '0' ? 'checked' : '' }}>
                    <span class="material-symbols-outlined" aria-hidden="true">thumb_down</span>
                    いいえ
                  </label>
                </fieldset>
                <textarea class="textarea" name="message" placeholder="コメントを書く">{{ old('message') }}</textarea>
                <div class="form-actions" style="margin-top: 10px;">
                  <button class="button button--primary" type="submit">評価とコメントを投稿</button>
                </div>
              </form>
            @else
              <p style="color: var(--muted); margin-bottom: 12px;">評価とコメントは購入済みユーザーのみ投稿できます。</p>
            @endif
          @else
            <p style="color: var(--muted); margin-bottom: 12px;"><a class="nav-link" href="{{ route('login') }}">ログイン</a>するとコメントできます。</p>
          @endauth

          @forelse($content->comments as $comment)
            <div class="comment" id="comment-{{ $comment->id }}">
              <img class="avatar-sm" src="{{ $comment->user->avatar_url }}" alt="">
              <div>
                <strong><a href="{{ route('profiles.show', $comment->user) }}">{{ $comment->user->display_name }}</a></strong>
                <div class="comment__meta">
                  <span class="material-symbols-outlined" aria-hidden="true">{{ $comment->is_recommended ? 'thumb_up' : 'thumb_down' }}</span>
                  <span>{{ $comment->is_recommended ? 'おすすめ' : 'おすすめしない' }}</span>
                  <time datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->format('Y/m/d H:i') }}</time>
                </div>
                <p>{{ $comment->message }}</p>
              </div>
            </div>
          @empty
            <p style="color: var(--muted);">まだコメントはありません。</p>
          @endforelse
        </section>

        <section class="section">
          <div class="section-heading">
            <h2 class="section-title">{{ $content->author->display_name }}のコンテンツ</h2>
            <a class="nav-link" href="{{ route('profiles.show', $content->author) }}">もっと見る</a>
          </div>
          <div class="content-grid">
            @foreach($authorMore as $item)
              @include('partials.content-card', ['content' => $item])
            @endforeach
          </div>
        </section>

        <section class="section">
          <div class="section-heading">
            <h2 class="section-title">同じジャンルの人気コンテンツ</h2>
            <a class="nav-link" href="{{ route('search.advanced', ['genre' => $content->genre_id, 'sort' => 'favorites']) }}">もっと見る</a>
          </div>
          <div class="content-grid">
            @foreach($related as $item)
              @include('partials.content-card', ['content' => $item])
            @endforeach
          </div>
        </section>
      </article>

      <aside class="detail-side">
        <section class="panel">
          <h2>{{ $content->title }}</h2>
          <div class="meta-row">
            <img class="avatar-sm" src="{{ $content->author->avatar_url }}" alt="">
            <a href="{{ route('profiles.show', $content->author) }}">{{ $content->author->display_name }}</a>
          </div>
          <div class="spec-list">
            <div><span>評価率</span><strong>{{ $content->rating_label }}</strong></div>
            <div><span>評価数</span><strong>{{ number_format($content->ratings_count) }}</strong></div>
            <div><span>価格</span><strong>{{ $content->formatted_price }}</strong></div>
          </div>

          <div class="form-actions" style="margin-top: 18px;">
            @auth
              <form method="POST" action="{{ route('favorites.toggle', $content) }}" data-favorite-form>
                @csrf
                <button class="favorite-pill favorite-pill--large {{ $isFavorite ? 'is-active' : '' }}" type="submit" aria-label="お気に入り" data-favorite-button data-content-id="{{ $content->id }}" data-active-icon="{{ asset('assets/icons/favorite-on.svg') }}" data-inactive-icon="{{ asset('assets/icons/favorite-off.svg') }}">
                  <img class="favorite-icon" src="{{ asset($isFavorite ? 'assets/icons/favorite-on.svg' : 'assets/icons/favorite-off.svg') }}" alt="" data-favorite-icon>
                  <span data-favorite-count>{{ number_format($content->favorites_count) }}</span>
                </button>
              </form>
              @if($content->user_id === auth()->id())
                <a class="button button--ghost" href="{{ route('contents.edit', $content) }}">編集</a>
                <button class="button button--ghost purchase-button" type="button" disabled>購入できません</button>
              @elseif($hasPurchased && $purchaseOrder)
                <a class="button button--primary purchase-button" href="{{ route('purchases.show', $purchaseOrder) }}">購入済み</a>
              @elseif($hasPurchased)
                <a class="button button--primary purchase-button" href="{{ route('library.index') }}">ライブラリ追加済み</a>
              @elseif($inCart)
                <a class="button button--ghost purchase-button" href="{{ route('cart.index') }}">カートに入っています</a>
              @else
                <form method="POST" action="{{ route('cart.store', $content) }}">
                  @csrf
                  <button class="button button--primary purchase-button" type="submit">{{ $content->price === 0 ? 'ライブラリに追加' : '購入' }}</button>
                </form>
              @endif
            @else
              <a class="button button--primary purchase-button" href="{{ route('login') }}">{{ $content->price === 0 ? 'ライブラリに追加' : '購入' }}</a>
            @endauth
          </div>
        </section>

        <section class="panel">
          <h2>データ情報</h2>
          <div class="spec-list">
            <div><span>ライセンス</span><strong>{{ $content->license_type }}</strong></div>
            <div><span>更新日時</span><strong>{{ $content->updated_at->format('Y/m/d') }}</strong></div>
            <div><span>販売日時</span><strong>{{ optional($content->published_at)->format('Y/m/d') }}</strong></div>
            <div><span>動作環境</span><strong>{{ $content->environment ?: '指定なし' }}</strong></div>
            <div><span>合計データサイズ</span><strong>{{ number_format($content->file_size_mb, 2) }}MB</strong></div>
          </div>
        </section>
      </aside>
    </div>
  </main>

  @include('layouts.footer')
@endsection
