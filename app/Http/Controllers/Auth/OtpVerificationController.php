<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Services\OtpVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OtpVerificationController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if ($request->user()?->isVerified) {
            return redirect()->route('home');
        }

        return view('auth.verify-otp');
    }

    public function store(VerifyOtpRequest $request, OtpVerificationService $otp): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isVerified) {
            return redirect()->route('home');
        }

        if (! $otp->verify($user->email, $request->string('otp')->toString())) {
            return back()->withErrors(['otp' => __('Invalid or expired verification code.')]);
        }

        $user->forceFill(['isVerified' => true])->save();

        return redirect()->intended(route('home', absolute: false))->with('status', 'email-verified');
    }

    public function resend(Request $request, OtpVerificationService $otp): RedirectResponse
    {
        $user = $request->user();

        if (! $user || $user->isVerified) {
            return redirect()->route('home');
        }

        $otp->sendForUser($user);

        return back()->with('status', 'verification-code-sent');
    }
}
