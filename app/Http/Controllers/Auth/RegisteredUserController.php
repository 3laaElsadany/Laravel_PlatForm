<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\OtpVerificationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request, OtpVerificationService $otp): RedirectResponse
    {
        $user = User::create([
            'fullname' => $request->string('fullname')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => Hash::make($request->string('password')->toString()),
            'role' => User::ROLE_STUDENT,
            'isVerified' => false,
        ]);

        event(new Registered($user));

        $otp->sendForUser($user);

        Auth::login($user);

        return redirect()->route('verification.otp.show');
    }
}
