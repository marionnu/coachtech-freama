@extends('layouts.app')

@section('content')
<div class="page--address">
  <div class="container address-edit">
    <h2 class="page-title center">住所の変更</h2>

    <form method="POST" action="{{ route('purchase.address.update', $item) }}" class="card form-vert">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label for="postal_code">郵便番号</label>
        <input id="postal_code" name="postal_code" type="text"
               value="{{ old('postal_code', $user->postal_code) }}">
        @error('postal_code') <p class="error">{{ $message }}</p> @enderror
      </div>

      <div class="form-group">
        <label for="address">住所</label>
        <input id="address" name="address" type="text"
               value="{{ old('address', $user->address) }}">
        @error('address') <p class="error">{{ $message }}</p> @enderror
      </div>

      <div class="form-group">
        <label for="building">建物名</label>
        <input id="building" name="building" type="text"
               value="{{ old('building', $user->building) }}">
        @error('building') <p class="error">{{ $message }}</p> @enderror
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-primary">更新する</button>
      </div>
    </form>
  </div>
</div>
@endsection
