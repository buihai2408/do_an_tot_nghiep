<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Inertia::share([
            'appName' => config('app.name', 'Coffee Shop'),
        ]);

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            return (new MailMessage)
                ->subject('Khôi phục mật khẩu')
                ->greeting('Xin chào!')
                ->line('Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu khôi phục mật khẩu cho tài khoản của bạn.')
                ->action('Khôi phục mật khẩu', url(route('password.reset', [
                    'token' => $token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false)))
                ->line('Đường dẫn khôi phục mật khẩu này sẽ hết hạn trong ' . Config::get('auth.passwords.'.Config::get('auth.defaults.passwords').'.expire') . ' phút.')
                ->line('Nếu bạn không yêu cầu khôi phục mật khẩu, bạn không cần phải làm gì thêm.')
                ->salutation('Trân trọng, ' . config('app.name', 'Coffee Shop'));
        });
    }
}
