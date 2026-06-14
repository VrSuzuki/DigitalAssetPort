<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="@yield('description', 'DigitalAssetPortは、テンプレート、教材、システム、動画素材などのデジタルデータを販売・配布できるWebプラットフォームです。')">
  <meta name="keywords" content="DigitalAssetPort, デジタルコンテンツ, テンプレート, 教材, Laravel, ポートフォリオ">

  <meta property="og:title" content="@yield('og_title', 'DigitalAssetPort')">
  <meta property="og:description" content="@yield('og_description', 'あらゆるデジタルデータが共有・販売されるプラットフォームです。')">
  <meta property="og:type" content="@yield('og_type', 'website')">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:image" content="{{ asset('assets/dap-hero.png') }}">

  <title>@yield('title', 'DigitalAssetPort')</title>

  <link rel="icon" href="{{ asset('assets/dap-logo.svg') }}">
  <link rel="apple-touch-icon" href="{{ asset('assets/dap-logo.svg') }}">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700;900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,500,0,0" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
  <script defer src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
  @stack('styles')
</head>
<body class="@yield('body_class')">
  @yield('body')
  @stack('scripts')
</body>
</html>
