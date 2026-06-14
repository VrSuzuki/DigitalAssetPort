@extends('layouts.base')

@section('title', 'DigitalAssetPort | デジタルデータのマーケット')
@section('body_class', 'page-marketplace')

@section('body')
  @include('layouts.header')

  <main class="app-main">
    @include('partials.flash')

    <section class="search-summary">
      <div>
        <p class="section-eyebrow">Marketplace</p>
        <h1 class="section-title">
          @if(request('keyword'))
            検索キーワード: {{ request('keyword') }}
          @else
            すべてのコンテンツ
          @endif
        </h1>
        <div class="filter-tags">
          @foreach($activeFilters as $filter)
            @php
              $removeKeys = $filter['remove'] ?? [$filter['key']];
              $params = request()->except(array_merge($removeKeys, ['page']));
            @endphp
            <a class="filter-tag" href="{{ route('home', $params) }}">
              {{ $filter['label'] }}
              <span aria-hidden="true">×</span>
            </a>
          @endforeach
        </div>
      </div>
      <div class="search-summary__actions">
        <a class="button button--soft" href="{{ route('home') }}">検索条件をリセット</a>
      </div>
    </section>

    <div class="layout-grid">
      <aside class="sidebar" aria-label="サイドメニュー">
        <h2>ジャンル</h2>
        <div class="side-list" data-collapsible-list data-collapsed-count="6">
          @foreach($genres as $genre)
            <a href="{{ route('home', ['genre' => $genre->id]) }}" data-collapsible-item @if($loop->iteration > 6) hidden @endif>
              <span>{{ $genre->name }}</span>
              <strong>{{ $genre->contents_count }}</strong>
            </a>
          @endforeach
          @if($genres->count() > 6)
            <button class="side-list__more" type="button" data-collapsible-toggle>もっと表示する</button>
          @endif
        </div>

        <h2 style="margin-top: 24px;">フォロー中</h2>
        <div class="side-list" data-collapsible-list data-collapsed-count="3">
          @auth
            @forelse($followingUsers as $following)
              <a href="{{ route('profiles.show', $following) }}" data-collapsible-item @if($loop->iteration > 3) hidden @endif>
                <span>{{ $following->display_name }}</span>
                <strong>{{ $following->contents_count }}</strong>
              </a>
            @empty
              <span>まだフォローはありません。</span>
            @endforelse
            @if($followingUsers->count() > 3)
              <button class="side-list__more" type="button" data-collapsible-toggle>もっと表示する</button>
            @endif
          @else
            <a href="{{ route('login') }}">ログインして表示</a>
          @endauth
        </div>

        <h2 style="margin-top: 24px;">投稿者一覧</h2>
        <div class="side-list" data-collapsible-list data-collapsed-count="3">
          @foreach($authors->filter(fn ($author) => filled($author->handle))->values() as $author)
            <a href="{{ route('profiles.show', $author) }}" data-collapsible-item @if($loop->iteration > 3) hidden @endif>
              <span>{{ $author->display_name }}</span>
              <strong>{{ $author->contents_count }}</strong>
            </a>
          @endforeach
          @if($authors->count() > 3)
            <button class="side-list__more" type="button" data-collapsible-toggle>もっと表示する</button>
          @endif
        </div>
      </aside>

      <section aria-labelledby="items-title">
        <div class="section-heading">
          <div>
            <p class="section-eyebrow">Assets</p>
            <h2 class="section-title" id="items-title">投稿されたコンテンツ</h2>
          </div>
          <div class="listing-toolbar">
            <strong>{{ number_format($contents->total()) }}件ヒット</strong>
            <form class="listing-toolbar__form" action="{{ route('home') }}" method="GET">
              @foreach(request()->except(['sort', 'per_page', 'page']) as $key => $value)
                @if(is_array($value))
                  @foreach($value as $item)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                  @endforeach
                @else
                  <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
              @endforeach
              <select class="select" name="per_page" aria-label="表示件数" onchange="this.form.submit()">
                @foreach([20, 50, 100] as $count)
                  <option value="{{ $count }}" {{ (int) request('per_page', 20) === $count ? 'selected' : '' }}>{{ $count }}件</option>
                @endforeach
              </select>
              <select class="select" name="sort" aria-label="表示順序" onchange="this.form.submit()">
                @foreach($sorts as $key => $label)
                  <option value="{{ $key }}" {{ request('sort', 'newest') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
            </form>
          </div>
        </div>

        @if($contents->count())
          <div class="content-grid">
            @foreach($contents as $content)
              @include('partials.content-card', ['content' => $content])
            @endforeach
          </div>
          <div class="pagination">{{ $contents->links() }}</div>
        @else
          <div class="empty-state">条件に合うコンテンツがありません。</div>
        @endif
      </section>
    </div>
  </main>

  @include('layouts.footer')
@endsection
