@extends('layouts.app')

@section('content')
<div class="container purchase purchase-compact">
  <div class="grid">

    <section class="grid__left">

      <div class="product-row">
        <img src="{{ $item->thumbnail_url ?? asset('img/placeholder.png') }}"
             alt="{{ $item->name }}" class="product-thumb">
        <div class="product-meta">
          <div class="product-name">{{ $item->name }}</div>
          <div class="product-price">¥{{ number_format($item->price) }}</div>
          @if(method_exists($item,'isSold') && $item->isSold())
            <div class="error">※売り切れ</div>
          @endif
        </div>
      </div>

      <hr class="sep">

      <h3 class="section__title">支払い方法</h3>
      <form id="purchase-form" method="POST" action="{{ route('purchase.store', $item) }}">
        @csrf
        <select name="payment_method" id="payment_method" class="form-select" required>
          <option value="" disabled {{ old('payment_method') ? '' : 'selected' }}>選択してください</option>
          <option value="konbini" @selected(old('payment_method')==='konbini')>コンビニ払い</option>
          <option value="card"    @selected(old('payment_method')==='card')>カード支払い</option>
        </select>
        @error('payment_method')
          <div class="error">{{ $message }}</div>
        @enderror
      </form>

      <hr class="sep">

      <h3 class="section__title">配送先</h3>
      @php($u = auth()->user())
      <div class="address">
        <div>
          <div>〒 {{ $u?->postal_code ?? 'XXX-YYYY' }}</div>
          <div>{{ $u?->address ?? 'ここには住所が入ります' }}</div>
          <div>{{ $u?->building }}</div>
        </div>
        <a href="{{ route('purchase.address.edit',$item) }}" class="link">変更する</a>
      </div>
    </section>

    <aside>
      <div class="summary">
        <div class="summary__row">
          <span>商品代金</span>
          <strong>¥{{ number_format($item->price) }}</strong>
        </div>
        <div class="summary__row">
          <span>支払い方法</span>
          <span id="pm_label">—</span>
        </div>
      </div>

      <button
        form="purchase-form"
        class="btn-buy"
        {{ (method_exists($item,'isSold') && $item->isSold()) ? 'disabled' : '' }}
      >購入する</button>
    </aside>

  </div>
</div>

<script>
  const sel = document.getElementById('payment_method');
  const label = document.getElementById('pm_label');
  function render() {
    label.textContent =
      sel?.value === 'card'    ? 'カード支払い' :
      sel?.value === 'konbini' ? 'コンビニ払い' : '—';
  }
  sel?.addEventListener('change', render);
  render();
</script>
@endsection
