<?php

namespace App\Providers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use App\Providers\RouteServiceProvider;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 登録直後は必ずメール認証画面へ
    $this->app->singleton(RegisterResponse::class, function () {
        return new class implements RegisterResponse {
            public function toResponse($request)
            {
                return redirect('/email/verify');
            }
        };
    });

    // ログイン直後は intended or HOME（/dashboard）へ
    $this->app->singleton(LoginResponse::class, function () {
        return new class implements LoginResponse {
            public function toResponse($request)
            {
                return redirect()->intended(RouteServiceProvider::HOME);
            }
        };
    });
    }

    public function boot(): void
{
    Fortify::createUsersUsing(CreateNewUser::class);

    Fortify::registerView(function () {
        return view('auth.register');
    });

    Fortify::loginView(function () {
        return view('auth.login');
    });

    Fortify::verifyEmailView(fn() => view('auth.verify-email'));

    // ここを追加 ↓↓↓
    Fortify::authenticateUsing(function (Request $request) {
        // LoginRequestのルールでバリデーション
        $loginRequest = new LoginRequest();
        Validator::make(
            $request->all(),
            $loginRequest->rules(),
            $loginRequest->messages() ?? [],
            $loginRequest->attributes()
        )->validate();

        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            return $user;
        }
        return null;
    });
    // ↑↑↑ ここまで追加

    RateLimiter::for('login', function (Request $request) {
        $email = (string) $request->email;
        return Limit::perMinute(10)->by($email . $request->ip());
    });
}
}
