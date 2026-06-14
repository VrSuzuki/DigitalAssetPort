<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

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
        $googleUser = Socialite::driver('google')->user();
        $email = Str::lower($googleUser->getEmail());

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
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(40)),
            ]);
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
