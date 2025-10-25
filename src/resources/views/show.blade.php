@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items.css') }}">
@endsection

@section('content')

<div class="container container--detail">
  <div class="detail">
    <div class="detail__image">
      <img src="{{ $item->thumbnail_url ?? 'https://placehold.co/800x800?text=%E5%95%86%E5%93%81%E7%94%BB%E5%83%8F' }}"
           alt="{{ $item->item_name }}">
    </div>

    <div class="detail__info">
      <h1 class="detail__title">{{ $item->item_name }}</h1>
{{-- ↓ 見本どおり、タイトル直下に小さく表示 --}}
<div class="brandline">ブランド：{{ $item->brand_name ?: '－' }}</div>
      <div class="brandline">{{ $item->brand_name ?? '-' }}</div>

      <div class="price-line">
        <span class="price">¥{{ number_format($item->price) }}</span>
        <span class="tax">（税込み）</span>
      </div>

      <div class="socials">
        @auth
          <form method="POST" action="{{ $item->is_favorited ? route('items.unfavorite',$item) : route('items.favorite',$item) }}">
            @csrf
            @if($item->is_favorited) @method('DELETE') @endif
            <button type="submit" class="like-btn {{ $item->is_favorited ? 'is-on' : '' }}">
              ♡ {{ $item->favorites_count ?? $item->favorites()->count() }}
            </button>
          </form>
        @else
          <a class="like-btn" href="{{ route('login') }}">
            ♡ {{ $item->favorites_count ?? $item->favorites()->count() }}
          </a>
        @endauth
        {{-- ← ここを withCount に合わせる --}}
        <span>💬 {{ $item->comments_count ?? $item->comments()->count() }}</span>
      </div>

      <a href="{{ auth()->check() ? route('purchase.create',$item) : route('login') }}" class="btn-primary btn-wide">
        購入手続きへ
      </a>

      <section class="section">
        <h2 class="section__title">商品説明</h2>
        <p>{!! nl2br(e($item->description ?? '')) !!}</p>
      </section>

      <section class="section">
        <h2 class="section__title">商品の情報</h2>
        <div class="row">
  <span class="muted">カテゴリー</span>
  @if($item->categories->isNotEmpty())
    <span>{{ $item->categories->pluck('name')->join(' / ') }}</span>
  @else
    <span>-</span>
  @endif
</div>
        <div class="row"><span class="muted">商品の状態</span><span>{{ $item->condition_label ?? '-' }}</span></div>
        <div class="row"><span class="muted">ステータス</span><span>{{ $item->status_label ?? '-' }}</span></div>
      </section>

      {{-- ======== コメント一覧 ======== --}}
      <section class="section">
        {{-- 件数は withCount('comments') を想定 --}}
        <h2 class="section__title">コメント ({{ $item->comments_count ?? $item->comments()->count() }})</h2>

        @forelse($item->comments as $c)
          <div class="comment">
            <div class="avatar"></div>
            <div class="comment__body">
              <div class="comment__name">{{ $c->user->name }}</div>
              <p class="muted" style="margin:0 0 4px;">{{ $c->created_at->diffForHumans() }}</p>
              <div>{{ e($c->body) }}</div>
            </div>
          </div>
        @empty
          <div class="comment" style="opacity:.9">
            <div class="avatar"></div>
            <div class="comment__body">
              <div class="comment__name">guest</div>
              <input class="comment__input" type="text" placeholder="こちらにコメントが入ります。" disabled>
            </div>
          </div>
        @endforelse
      </section>

      {{-- ======== 商品へのコメント ======== --}}
      <section class="section">
        <h2 class="section__title">商品のコメント</h2>

        <form method="POST" action="{{ route('items.comments.store',$item) }}">
          @csrf
          {{-- FormRequestに合わせて255文字上限＋old保持 --}}
          <textarea name="body" class="textarea" rows="5" maxlength="255" required
                    placeholder="コメントを入力">{{ old('body') }}</textarea>
          @error('body') <div class="muted" style="color:#c00;">{{ $message }}</div> @enderror

          <button class="btn-primary" type="submit">コメントを送信する</button>

          @guest
            <div class="muted" style="margin-top:6px;font-size:12px;">
              ※送信するとログイン画面に遷移します（ログイン後に元のページへ戻ります）。
            </div>
          @endguest
        </form>
      </section>

    </div>
  </div>
</div>
@endsection
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif
