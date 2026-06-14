@extends('layouts.base')

@section('title', ($content->exists ? 'コンテンツ情報を編集' : 'コンテンツ投稿').' | DigitalAssetPort')

@section('body')
  @include('layouts.header')

  <main class="app-main">
    <section class="panel">
      <div class="section-heading">
        <div>
          <p class="section-eyebrow">Upload</p>
          <h1 class="section-title">{{ $content->exists ? 'コンテンツ情報を編集' : 'コンテンツを投稿' }}</h1>
        </div>
      </div>
      @include('partials.errors')

      <form method="POST" action="{{ $content->exists ? route('contents.update', $content) : route('contents.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        @if($content->exists)
          @method('PUT')
        @endif
        <div class="form-grid">
          <div class="field">
            <label for="title">コンテンツ名</label>
            <input class="input" id="title" name="title" value="{{ old('title', $content->title) }}">
          </div>
          <div class="field">
            <label for="price">価格</label>
            <input class="input" id="price" type="number" name="price" min="0" max="100000" step="100" value="{{ old('price', $content->price) }}">
          </div>
          <div class="field">
            <label for="genre_id">ジャンル</label>
            <select class="select" id="genre_id" name="genre_id">
              @foreach($genres as $genre)
                <option value="{{ $genre->id }}" {{ (int) old('genre_id', $content->genre_id) === $genre->id ? 'selected' : '' }}>{{ $genre->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label for="sub_genre_id">サブジャンル</label>
            <select class="select" id="sub_genre_id" name="sub_genre_id" data-subgenre-select data-parent-select="genre_id">
              @foreach($genres as $genre)
                @foreach($genre->subGenres as $subGenre)
                  <option value="{{ $subGenre->id }}" data-genre-id="{{ $genre->id }}" {{ (int) old('sub_genre_id', $content->sub_genre_id) === $subGenre->id ? 'selected' : '' }}>{{ $subGenre->name }}</option>
                @endforeach
              @endforeach
            </select>
          </div>
          <div class="field">
            <label for="format">形式</label>
            <select class="select" id="format" name="format">
              @foreach($formats as $key => $label)
                <option value="{{ $key }}" {{ old('format', $content->format) === $key ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label for="license_type">ライセンスの種類</label>
            <input class="input" id="license_type" name="license_type" value="{{ old('license_type', $content->license_type) }}">
          </div>
          <div class="field field--full">
            <label for="content_images_input">コンテンツ画像</label>
            <div class="image-repeater" data-image-repeater data-max-images="20">
              <div class="image-repeater__list" data-image-list>
                @if($content->exists && $content->images->count())
                  @foreach($content->images as $image)
                    <div class="image-record" data-image-record>
                      <img src="{{ $image->url }}" alt="登録済みコンテンツ画像{{ $loop->iteration }}">
                      <span>登録済み画像 {{ $loop->iteration }}</span>
                      <button class="image-record__remove" type="button" data-delete-image="{{ $image->id }}" aria-label="画像を削除">
                        <span class="material-symbols-outlined" aria-hidden="true">close</span>
                      </button>
                    </div>
                  @endforeach
                @elseif($content->thumbnail_path)
                  <div class="image-record">
                    <img src="{{ $content->thumbnail_url }}" alt="登録済みコンテンツ画像">
                    <span>登録済み画像</span>
                  </div>
                @endif
              </div>
              <label class="button button--primary image-upload__button" for="content_images_input">
                <span class="material-symbols-outlined" aria-hidden="true">add_photo_alternate</span>
                画像を追加
              </label>
              <input class="visually-hidden" id="content_images_input" type="file" name="images[]" accept="image/*" multiple data-file-input>
            </div>
          </div>
          <div class="field field--full">
            <label for="description">コンテンツ紹介文</label>
            <textarea class="textarea" id="description" name="description" maxlength="5000">{{ old('description', $content->description) }}</textarea>
          </div>
          <div class="field">
            <label for="tags">タグ</label>
            <input class="input" id="tags" name="tags" value="{{ old('tags', $content->exists ? $content->tags->pluck('name')->implode(', ') : '') }}">
          </div>
          <div class="field">
            <label for="environment">動作環境</label>
            <input class="input" id="environment" name="environment" value="{{ old('environment', $content->environment) }}">
          </div>
          <div class="field field--full">
            <label for="content_file">コンテンツファイル</label>
            <input class="input" id="content_file" type="file" name="content_file">
          </div>
        </div>
        <div class="form-actions" style="margin-top: 18px;">
          <a class="button button--ghost" href="{{ url()->previous() }}">戻る</a>
          <button class="button button--primary" type="submit">{{ $content->exists ? '更新' : '投稿' }}</button>
        </div>
      </form>
    </section>
  </main>

  @include('layouts.footer')
@endsection
