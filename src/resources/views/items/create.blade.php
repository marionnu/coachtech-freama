@extends('layouts.app')

@push('styles')
  <link rel="stylesheet"
        href="{{ asset('css/sell.css') }}?v={{ filemtime(public_path('css/sell.css')) }}">
@endpush

@section('content')
<div class="sell-wrap">
  <div class="sell-card">
    <h2 class="title">商品の出品</h2>

    <form action="{{ route('items.store') }}" method="post" enctype="multipart/form-data">
      @csrf

      <div class="form-grid">
      <div class="sec">
        <h3>商品画像</h3>
        <label class="image-drop">
          <input id="images" class="image-input" type="file" name="images[]" multiple accept="image/*">
          <span class="image-btn">画像を選択する</span>
        </label>
        <div id="preview" class="thumbs"></div>
        @error('images.*') <p class="err">{{ $message }}</p> @enderror
      </div>

      <div class="sec">
        <h3>商品の詳細</h3>

        <div class="sec">
          <h3>カテゴリ</h3>
          <div class="chips">
            @foreach($categories as $cat)
              <label class="chip">
                <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                       {{ in_array($cat->id, old('categories', [])) ? 'checked' : '' }}>
                <span>{{ $cat->name }}</span>
              </label>
            @endforeach
          </div>
          @error('categories') <p class="err">{{ $message }}</p> @enderror
        </div>

        <div class="sec">
          <h3>商品の状態</h3>
          <select name="condition" class="select" required>
            <option value="" disabled {{ old('condition') ? '' : 'selected' }}>選択してください</option>
            @foreach($conditions as $val => $label)
              <option value="{{ $val }}" {{ old('condition') == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
          @error('condition') <p class="err">{{ $message }}</p> @enderror
        </div>

        <div class="sec">
          <h3>商品名と説明</h3>
          <input class="input" name="name" value="{{ old('name') }}" placeholder="商品名" required>
          @error('name') <p class="err">{{ $message }}</p> @enderror

          <div style="height:10px"></div>

          <input class="input" name="brand_name" value="{{ old('brand_name') }}" placeholder="ブランド名">
          @error('brand_name') <p class="err">{{ $message }}</p> @enderror

          <div style="height:10px"></div>

          <textarea class="textarea" name="description" placeholder="商品の説明">{{ old('description') }}</textarea>
          @error('description') <p class="err">{{ $message }}</p> @enderror
        </div>

        <div class="sec">
          <h3>販売価格</h3>
          <div class="yen">
            <span>¥</span>
            <input class="input" type="number" name="price" min="1" step="1" value="{{ old('price') }}" required>
          </div>
          @error('price') <p class="err">{{ $message }}</p> @enderror
        </div>
      </div>
      </div>

      <button class="submit">出品する</button>
    </form>
  </div>
</div>

<script>
  document.getElementById('images')?.addEventListener('change', function(e){
    const box = document.getElementById('preview');
    box.innerHTML = '';
    [...e.target.files].slice(0,8).forEach(file=>{
      const img = document.createElement('img');
      img.src = URL.createObjectURL(file);
      box.appendChild(img);
    });
  });
</script>
@endsection
