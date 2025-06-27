<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckUserRole;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScheduledClassController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth'])->name('dashboard');

Route::get('/instructor/dashboard', function () {
    return view('instructor.dashboard');
})->middleware(['auth', CheckUserRole::class.':instructor'])->name('instructor.dashboard');

Route::resource('/instructor/schedule', ScheduledClassController::class)
    ->only(['index', 'create', 'store', 'destroy'])
    ->middleware('auth', CheckUserRole::class.':instructor');

Route::get('/member/dashboard', function () {
    return view('member.dashboard');
})->middleware(['auth', CheckUserRole::class.':member'])->name('member.dashboard');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', CheckUserRole::class.':admin'])->name('admin.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
