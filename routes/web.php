<?php

use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseCheckoutController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseRatingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyCoursesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/search', SearchController::class)->name('search');

Route::post('/locale', function (Request $request) {
    $locale = (string) $request->input('locale', 'en');
    $supported = ['en', 'ar'];

    if (! in_array($locale, $supported, true)) {
        $locale = config('app.locale', 'en');
    }

    $request->session()->put('locale', $locale);

    return redirect()->to((string) $request->input('redirect_to', url('/')));
})->name('locale.switch');

Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified.otp'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/verify-email-otp', [OtpVerificationController::class, 'show'])->name('verification.otp.show');
    Route::post('/verify-email-otp', [OtpVerificationController::class, 'store'])->name('verification.otp.store');
    Route::post('/verify-email-otp/resend', [OtpVerificationController::class, 'resend'])->name('verification.otp.resend');
});

Route::middleware(['auth', 'verified.otp'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/my-courses', MyCoursesController::class)->name('my-courses');

    Route::post('/courses/{course}/reviews', [ReviewController::class, 'store'])->name('courses.reviews.store');

    Route::post('/courses/{course}/rating', [CourseRatingController::class, 'store'])->name('courses.ratings.store');
    Route::delete('/courses/{course}/rating', [CourseRatingController::class, 'destroy'])->name('courses.ratings.destroy');

    Route::get('/courses/{course}/checkout', [CourseCheckoutController::class, 'show'])->name('courses.checkout.show');
    Route::post('/courses/{course}/checkout', [CourseCheckoutController::class, 'store'])->name('courses.checkout.store');
    Route::get('/courses/{course}/checkout/stripe/return', [CourseCheckoutController::class, 'stripeReturn'])->name('courses.checkout.stripe.return');
    Route::get('/courses/{course}/checkout/paypal/return', [CourseCheckoutController::class, 'paypalReturn'])->name('courses.checkout.paypal.return');
    Route::get('/courses/{course}/checkout/cancel', [CourseCheckoutController::class, 'cancel'])->name('courses.checkout.cancel');
});

require __DIR__.'/auth.php';
