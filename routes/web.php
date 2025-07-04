<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduledClassController;
use App\Http\Middleware\CheckUserRole;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth'])->name('dashboard');

/* Instructor routes */
Route::middleware(['auth', CheckUserRole::class.':instructor'])->group(function () {
    Route::get('/instructor/dashboard', fn () => view('instructor.dashboard'))->name('instructor.dashboard');
    Route::resource('/instructor/schedule', ScheduledClassController::class)
        ->only(['index', 'create', 'store', 'destroy']);
});

/* Member routes */
Route::middleware(['auth', CheckUserRole::class.':member'])->group(function () {
    Route::get('/member/dashboard', fn () => view('member.dashboard'))->name('member.dashboard');
    // Route::get('/member/schedule', [ScheduledClassController::class, 'index'])->name('member.schedule.index');
    // Route::get('/member/schedule/{id}', [ScheduledClassController::class, 'show'])->name('member.schedule.show');

    Route::get('/member/bookings', [BookingController::class, 'index'])->name('member.booking.index');
    Route::get('/member/booking', [BookingController::class, 'create'])->name('member.booking.create');
    Route::post('/member/booking', [BookingController::class, 'store'])->name('member.booking.store');
    Route::delete('/member/booking/{id}', [BookingController::class, 'destroy'])->name('member.booking.destroy');
});

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', CheckUserRole::class.':admin'])->name('admin.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
