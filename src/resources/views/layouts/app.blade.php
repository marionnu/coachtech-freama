<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'COACHTECH') }}</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sell.css') }}">
  @stack('styles')
  @yield('css')
</head>

<body>
  @php($isAuthPage = request()->routeIs('login','register','password.*','verification.*'))
  <header class="header">
  <div class="header__inner">
    {{-- 左：ロゴ（SVGに差し替え） --}}
    <a class="header__logo" href="{{ route('items.index') }}" aria-label="COACHTECH">
      <img src="{{ asset('svg/logo.svg') }}" alt="COACHTECH" class="header__logo-img">
    </a>

    {{-- 中央：検索 --}}
@unless($isAuthPage)
  <form action="{{ route('items.index') }}" method="get" class="header-search" role="search">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="なにをお探しですか？" aria-label="検索">
  </form>

  {{-- 右：ナビ --}}
  <nav class="header-nav" aria-label="グローバル">
    <ul class="header-nav__list">
      @auth
        <li><a href="{{ route('mypage.index') }}" class="header-nav__link">マイページ</a></li>
        <li>
          <form action="{{ route('logout') }}" method="post" class="inline-form">
            @csrf
            <button type="submit" class="header-nav__link linklike">ログアウト</button>
          </form>
        </li>
        <li><a href="{{ route('items.create') }}" class="btn-outline">出品</a></li>
      @endauth

      @guest
        @if (Route::has('login'))    <li><a href="{{ route('login') }}" class="header-nav__link">ログイン</a></li> @endif
        @if (Route::has('register')) <li><a href="{{ route('register') }}" class="header-nav__link">新規登録</a></li> @endif
      @endguest
    </ul>
  </nav>
@endunless
  </div>
</header>

  <main>
    {{-- ★ フラッシュメッセージ（購入成功/キャンセル、住所更新など） --}}
    @if (session('success'))
      <div class="flash flash--success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="flash flash--error">{{ session('error') }}</div>
    @endif

    @yield('content')
  </main>

  @yield('scripts')
</body>
</html>
