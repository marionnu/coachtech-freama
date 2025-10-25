@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items.css') }}">
@endsection

@section('content')
<div class="container container--wide">

  {{-- ヘッダ（ユーザー名＋編集ボタン） --}}
  <div style="display:flex;align-items:center;gap:16px;margin:12px 0 20px;">
    <img src="{{ $user->avatar_url ?? 'https://placehold.co/80x80?text=%20' }}"
         alt="" style="width:64px;height:64px;border-radius:50%;object-fit:cover;">
    <div style="font-weight:700;">{{ $user->name }}</div>
    <a href="{{ route('mypage.profile.edit') }}" class="btn-outline" style="margin-left:auto;">プロフィールを編集</a>
  </div>

  {{-- タブ（出品／購入） --}}
  <div class="tabs">
    <a class="tab {{ $tab==='sell' ? 'tab--active' : '' }}"
       href="{{ route('mypage.index', ['page'=>'sell']) }}">出品した商品</a>
    <a class="tab {{ $tab==='buy' ? 'tab--active' : '' }}"
       href="{{ route('mypage.index', ['page'=>'buy']) }}">購入した商品</a>
  </div>

  @if($items->isEmpty())
    <p style="margin:12px 0;">データがありません。</p>
  @else
    <div class="grid grid--catalog">
      @foreach($items as $item)
        @php
          $img = optional($item->images->first())->path ?? null;
          $src = $img
            ? (preg_match('#^https?://#', $img) ? $img : \Illuminate\Support\Facades\Storage::url($img))
            : 'https://placehold.co/600x600?text=%E5%95%86%E5%93%81%E7%94%BB%E5%83%8F';
          $isSold = method_exists($item,'isSold') ? $item->isSold() : !is_null($item->sold_at);
        @endphp
        <a class="card card--product" href="{{ route('items.show', $item) }}">
          <div class="thumb thumb--square">
            <img src="{{ $src }}" alt="{{ $item->item_name }}">
            @if($isSold)<span class="badge badge--sold">SOLD</span>@endif
          </div>
          <div class="meta meta--compact">
            <div class="product-name">{{ $item->item_name }}</div>
          </div>
        </a>
      @endforeach
    </div>
    <div class="pager">{{ $items->links() }}</div>
  @endif
</div>
@endsection
