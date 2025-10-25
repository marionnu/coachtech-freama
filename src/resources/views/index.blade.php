@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items.css') }}">
@endsection

@section('content')

@php
  $q = request('q');
  $recommendUrl = $q ? url('/?'.http_build_query(['q'=>$q])) : url('/');
  $mylistParams = ['tab'=>'mylist'] + ($q ? ['q'=>$q] : []);
  $mylistUrl = url('/?'.http_build_query($mylistParams));
@endphp

<div class="tabs">
  <a class="tab {{ ($activeTab ?? 'recommend') === 'recommend' ? 'tab--active' : '' }}" href="{{ $recommendUrl }}">おすすめ</a>
  <a class="tab {{ ($activeTab ?? '') === 'mylist' ? 'tab--active' : '' }}" href="{{ $mylistUrl }}">マイリスト</a>
</div>

<div class="container container--wide">
  @if($items->count() === 0)
    @if(!empty($suppressEmptyMessage))
      {{-- 未認証ユーザーのマイリスト：何も表示しない --}}
    @elseif(request()->filled('q'))
      <p>「{{ request('q') }}」に一致する商品が見つかりません。</p>
    @elseif(($activeTab ?? 'recommend') === 'mylist')
      <p>マイリストに商品がありません。</p>
    @else
      <p>商品がありません。</p>
    @endif
  @else
    <div class="grid grid--catalog">
      @foreach($items as $item)
        @php
          // 画像URL
          $img = optional($item->images->first())->path ?? null;
          $src = $img
            ? (preg_match('#^https?://#', $img) ? $img : \Illuminate\Support\Facades\Storage::url($img))
            : 'https://placehold.co/600x600?text=%E5%95%86%E5%93%81%E7%94%BB%E5%83%8F';

          // SOLD 判定（Item::isSold() がある前提。無ければ !is_null($item->sold_at) でもOK）
          $isSold = method_exists($item, 'isSold') ? $item->isSold() : !is_null($item->sold_at);
        @endphp

        <a class="card card--product" href="{{ route('items.show', $item) }}">
          <div class="thumb thumb--square">
            <img src="{{ $src }}" alt="{{ $item->item_name }}">
            @if($isSold)
              <span class="badge badge--sold">SOLD</span>
            @endif
          </div>

          <div class="meta meta--compact">
            <div class="product-name">{{ $item->item_name }}</div>
            @isset($item->favorites_count)
              <div style="font-size:12px;color:#666;margin-top:2px;">♡ {{ $item->favorites_count }}</div>
            @endisset
          </div>
        </a>
      @endforeach
    </div>

    <div class="pager">
      {{ $items->links() }}
    </div>
  @endif
</div>
@endsection
