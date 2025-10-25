@extends('layouts.app')

@section('content')
<div style="max-width: 800px; margin: 40px auto; background: #fff; padding: 40px; text-align: center; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

    <p style="margin-bottom: 30px;">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    {{-- ★ 変更：赤い有効ボタンに（共通 btn-primary を使用） --}}
    <a href="#" class="btn-primary" style="display:inline-block;">認証はこちらから</a>

    {{-- ステータスメッセージ --}}
    @if (session('status') === 'verification-link-sent')
        <p style="color: green; margin-top: 20px;">
            認証メールを再送しました。受信箱を確認してください。
        </p>
    @endif

    {{-- 認証メール再送 --}}
    <form method="POST" action="{{ route('verification.send') }}" style="margin-top: 20px;">
        @csrf
        <button type="submit" style="background: none; border: none; color: #3490dc; text-decoration: underline; cursor: pointer;">
            認証メールを再送する
        </button>
    </form>
</div>
@endsection
