@extends('layouts.base')

@section('title', 'アカウント登録 | DigitalAssetPort')

@section('body')
  @include('layouts.header')

  <main class="auth-main">
    <section class="auth-card">
      <h1>アカウント登録画面</h1>
      @include('partials.flash')
      @include('partials.errors')
      <a class="social-auth-button" href="{{ route('auth.google.redirect') }}">
        <img class="social-auth-button__icon" src="{{ asset('assets/icons/google-g.svg') }}" alt="" aria-hidden="true">
        Googleアカウントで登録
      </a>
      <div class="auth-divider"><span>またはメールアドレスで登録</span></div>
      <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf
        <div class="form-grid form-grid--single">
          <div class="field">
            <label for="handle">ユーザーID</label>
            <input class="input" id="handle" name="handle" value="{{ old('handle') }}" autofocus>
          </div>
          <div class="field">
            <label for="email">メールアドレス</label>
            <input class="input" id="email" type="email" name="email" value="{{ old('email') }}">
          </div>
          <div class="field">
            <label for="password">設定パスワード</label>
            <input class="input" id="password" type="password" name="password">
          </div>
          <div class="field">
            <label for="password_confirmation">パスワード確認</label>
            <input class="input" id="password_confirmation" type="password" name="password_confirmation">
          </div>
        </div>
        <div class="form-actions" style="margin-top: 18px;">
          <button class="button button--primary" type="submit">登録する</button>
          <a class="button button--ghost" href="{{ route('login') }}">ログインはこちら</a>
        </div>
      </form>
    </section>
  </main>
@endsection
