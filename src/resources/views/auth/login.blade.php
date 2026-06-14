@extends('layouts.base')

@section('title', 'ログイン | DigitalAssetPort')

@section('body')
  @include('layouts.header')

  <main class="auth-main">
    <section class="auth-card">
      <h1>ログイン画面</h1>
      @include('partials.flash')
      @include('partials.errors')
      <a class="social-auth-button" href="{{ route('auth.google.redirect') }}">
        <img class="social-auth-button__icon" src="{{ asset('assets/icons/google-g.svg') }}" alt="" aria-hidden="true">
        Googleアカウントでログイン
      </a>
      <div class="auth-divider"><span>またはメールアドレスでログイン</span></div>
      <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf
        <div class="form-grid form-grid--single">
          <div class="field">
            <label for="email">メールアドレス</label>
            <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" autofocus>
          </div>
          <div class="field">
            <label for="password">パスワード</label>
            <input class="input" id="password" type="password" name="password">
          </div>
        </div>
        <div class="form-actions" style="margin-top: 18px;">
          <button class="button button--primary" type="submit">ログインする</button>
          <a class="button button--ghost" href="{{ route('register') }}">アカウント登録はこちら</a>
        </div>
      </form>
    </section>
  </main>
@endsection
