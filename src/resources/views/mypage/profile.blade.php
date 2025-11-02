@extends('layouts.app')

@section('content')
<div class="container address-edit page--profile">
  <h2 class="page-title center">プロフィール設定</h2>

  @php
    $avatarPath = optional($profile)->path;
  @endphp

  <div class="card">
    <form class="form-vert" method="POST" action="{{ route('mypage.profile.update') }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="avatar-row">
        @if ($avatarPath)
          <img src="{{ Storage::url($avatarPath) }}" alt="avatar"
               style="width:72px;height:72px;border-radius:50%;object-fit:cover;background:#eee;">
        @else
          <div class="avatar-ph" aria-label="プロフィール画像未設定"></div>
        @endif

        <label class="btn-primary btn-upload" style="cursor:pointer;">
          画像を選択する
          <input type="file" name="avatar" accept="image/*" hidden>
        </label>
      </div>
      @error('avatar')<div class="error">{{ $message }}</div>@enderror

      <div class="form-group">
        <label for="name">ユーザー名</label>
        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" autocomplete="name">
        @error('name')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label for="postal_code">郵便番号</label>
        <input id="postal_code" type="text" name="postal_code" value="{{ old('postal_code', optional($profile)->postal_code) }}" inputmode="numeric" autocomplete="postal-code">
        @error('postal_code')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label for="address">住所</label>
        <input id="address" type="text" name="address" value="{{ old('address', optional($profile)->address) }}" autocomplete="street-address">
        @error('address')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label for="building">建物名</label>
        <input id="building" type="text" name="building" value="{{ old('building', optional($profile)->building) }}">
        @error('building')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="form-actions">
        <button class="btn-primary" type="submit">更新する</button>
      </div>
    </form>
  </div>
</div>
@endsection
