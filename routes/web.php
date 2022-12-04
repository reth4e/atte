<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RestController;
use App\Http\Controllers\TestMailController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('attendance')->group(function () {
    Route::get('start', [AttendanceController::class, 'start']);
    Route::get('end', [AttendanceController::class,'end']);
});

//下のミドルウェアauthをverifiedにすると認証画面　エラー　Connection could not be established with host mailhog →mailtrapに変更
Route::group(['middleware' => 'verified' ],function() {
    Route::get('/', [AttendanceController::class, 'index']);
    Route::get('attendance/{num}', [AttendanceController::class,'attendances']);
});


Route::prefix('rest')->group(function () {
    Route::get('start', [RestController::class, 'start']);
    Route::get('end', [RestController::class,'end']);
});

//メールの送信テスト mailtrap
Route::get('/mail',[TestMailController::class,'send']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');


// Route::get('/email/verify', function () {
//     return view('auth.verify-email');
// })->middleware('auth')->name('verification.notice');

// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $request->fulfill();

//     return redirect('/home');
// })->middleware(['auth', 'signed'])->name('verification.verify');

// Route::post('/email/verification-notification', function (Request $request) {
//     $request->user()->sendEmailVerificationNotification();

//     return back()->with('message', 'Verification link sent!');
// })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

require __DIR__.'/auth.php';
