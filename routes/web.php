<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\PdfController;

Route::post('/pdf-upload', [PdfController::class, 'upload'])->name('pdf.upload');

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::middleware(['auth','admin'])->prefix('admin')->group(function () {

//     Route::resource('teachers', TeacherController::class);

// });

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('courses', CourseController::class);
    Route::resource('teachers', TeacherController::class);
});


Route::view('/', 'home')->name('home');
Route::view('/about', 'about');
Route::view('/courses', 'courses');
Route::view('/contact', 'contact');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
