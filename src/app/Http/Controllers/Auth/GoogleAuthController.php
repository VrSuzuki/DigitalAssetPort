<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        if (!config('services.google.client_id') || !config('services.google.client_secret')) {
            return back()->with('status', 'Google認証を使うには .env に GOOGLE_CLIENT_ID と GOOGLE_CLIENT_SECRET を設定してください。');
        }

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException $exception) {
            return redirect()
                ->route('login')
                ->withErrors(['google' => 'Google認証のセッションが切れました。もう一度Googleアカウントでログインしてください。']);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('login')
                ->withErrors(['google' => 'Google認証サーバーに接続できませんでした。Dockerを再起動してからもう一度お試しください。']);
        }

        $email = Str::lower($googleUser->getEmail());

        if (!$email) {
            return redirect()
                ->route('login')
                ->withErrors(['google' => 'Googleアカウントからメールアドレスを取得できませんでした。メール公開設定を確認してください。']);
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            $user->forceFill([
                'google_id' => $user->google_id ?: $googleUser->getId(),
                'google_avatar_url' => $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?: now(),
            ])->save();
        } else {
            $user = User::create([
                'handle' => $this->uniqueHandle($googleUser->getName(), $email),
                'name' => $googleUser->getName() ?: Str::before($email, '@'),
                'nickname' => $googleUser->getName(),
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'google_avatar_url' => $googleUser->getAvatar(),
                'password' => Hash::make(Str::random(40)),
            ]);

            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()
            ->route('profiles.edit')
            ->with('status', 'Googleアカウント認証が完了しました。プロフィールを設定してください。');
    }

    private function uniqueHandle(?string $name, string $email): string
    {
        $base = Str::slug($name ?: Str::before($email, '@'), '_') ?: 'google_user';
        $base = Str::limit($base, 24, '');
        $handle = $base;
        $count = 2;

        while (User::where('handle', $handle)->exists()) {
            $handle = Str::limit($base, 24, '').'_'.$count++;
        }

        return $handle;
    }
}
