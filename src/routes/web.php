<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/* ---------- Public ---------- */
Route::get('/',            [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');
Route::get('/mylist', function () {
    if (!auth()->check()) {
        session()->put('url.intended', url('/?tab=mylist'));
        return redirect()->route('login');
    }
    return redirect()->route('items.index', ['tab' => 'mylist']);
})->name('items.mylist');

/* ---------- auth (※プロフィール編集は verified 不要) ---------- */
Route::middleware(['auth'])->group(function () {
    // ★ 登録直後に来られるよう verified を要求しない
    Route::get('/mypage/profile', [MyPageController::class, 'editProfile'])->name('mypage.profile.edit');
    Route::put('/mypage/profile', [MyPageController::class, 'updateProfile'])->name('mypage.profile.update');

    // ★（任意）メール認証ルートの明示と完了後の遷移をプロフィールに固定
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('mypage.profile.edit'); // 認証完了後はプロフィールへ
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->name('verification.send');
});

/* ---------- auth ---------- */
Route::middleware('auth')->group(function () {
    Route::post  ('/items/{item}/favorite', [FavoriteController::class, 'store' ])->name('items.favorite');
    Route::delete('/items/{item}/favorite', [FavoriteController::class, 'destroy'])->name('items.unfavorite');
    Route::post('/items/{item}/comments',   [CommentController::class, 'store'])->name('items.comments.store');
});

/* ---------- auth + verified ---------- */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('items.index', ['tab' => 'mylist']))->name('dashboard');

    Route::get ('/purchase/{item}',         [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('/purchase/{item}',         [PurchaseController::class, 'store' ])->name('purchase.store');

    Route::get ('/purchase/address/{item}', [PurchaseController::class, 'editAddress' ])->name('purchase.address.edit');
    Route::put ('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

    Route::get('/purchase/{item}/success',  [PurchaseController::class, 'success'])->name('purchase.success');
    Route::get('/purchase/{item}/cancel',   [PurchaseController::class, 'cancel' ])->name('purchase.cancel');

    Route::get ('/sell',  [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store' ])->name('items.store');

    Route::get('/mypage', [MyPageController::class, 'index'])->name('mypage.index');

    // ★ 重複していた /mypage/profile の verified 版は削除
});
