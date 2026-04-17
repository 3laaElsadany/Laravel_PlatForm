<?php

namespace App\Services;

use App\Mail\OtpVerificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class OtpVerificationService
{
    private const TTL_SECONDS = 600;

    private function cacheKey(string $email): string
    {
        return 'email_otp:'.mb_strtolower($email);
    }

    public function sendForUser(User $user): void
    {
        $otp = (string) random_int(100000, 999999);

        Cache::put($this->cacheKey($user->email), $otp, self::TTL_SECONDS);

        Mail::to($user->email)->send(new OtpVerificationMail($otp, $user->fullname));
    }

    public function verify(string $email, string $otp): bool
    {
        $key = $this->cacheKey($email);
        $expected = Cache::get($key);

        if (! is_string($expected) || ! hash_equals($expected, $otp)) {
            return false;
        }

        Cache::forget($key);

        return true;
    }
}
