<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\OtpVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request, OtpVerificationService $otp): RedirectResponse
    {
        $user = $request->user();
        $emailChanged = $user->email !== $request->string('email')->toString();
        $oldAvatarPath = $user->avatar_path;

        $user->fill($request->safe()->except(['avatar', 'remove_avatar']));

        if ($request->boolean('remove_avatar')) {
            $user->avatar_path = null;
        }

        if ($request->hasFile('avatar')) {
            $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        }

        if ($emailChanged) {
            $user->isVerified = false;
        }

        $user->save();

        if ($oldAvatarPath && $oldAvatarPath !== $user->avatar_path) {
            Storage::disk('public')->delete($oldAvatarPath);
        }

        if ($emailChanged) {
            $otp->sendForUser($user);

            return Redirect::route('verification.otp.show')->with('status', 'email-changed-verify');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
