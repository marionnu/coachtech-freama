@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-form__content">
  <div class="login-form__heading">
    <h2>ログイン</h2>
  </div>

  {{-- ★ action を /login → route('login') に、CSRF はそのまま --}}
  <form class="form" action="{{ route('login') }}" method="post">
    @csrf

    <div class="form__group">
      <div class="form__group-title">
        <span class="form__label--item">メールアドレス</span>
      </div>
      <div class="form__group-content">
        <div class="form__input--text">
          {{-- ★ 必須/オートコンプリートを追加 --}}
          <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" />
        </div>
        <div class="form__error">
          @error('email') {{ $message }} @enderror
        </div>
      </div>
    </div>

    <div class="form__group">
      <div class="form__group-title">
        <span class="form__label--item">パスワード</span>
      </div>
      <div class="form__group-content">
        <div class="form__input--text">
          {{-- ★ 必須/オートコンプリートを追加 --}}
          <input type="password" name="password" required autocomplete="current-password" />
        </div>
        <div class="form__error">
          @error('password') {{ $message }} @enderror
        </div>
      </div>
    </div>

    <div class="form__button">
      {{-- ★ 文言：「ログインする」＆ 見本の赤ボタンに合わせて共通スタイルを付与 --}}
      <button class="form__button-submit btn-primary btn-wide" type="submit">ログインする</button>
    </div>
  </form>

  <div class="register__link">
    <a class="register__button-submit" href="{{ route('register') }}">会員登録の方はこちら</a>
  </div>
</div>
@endsection
