<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentCourseController;
use App\Http\Controllers\Student\LiveClassController;


Route::post('/pdf-upload', [PdfController::class, 'upload'])->name('pdf.upload');

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::middleware(['auth','admin'])->prefix('admin')->group(function () {

//     Route::resource('teachers', TeacherController::class);

// });

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('courses', CourseController::class);
    Route::resource('teachers', TeacherController::class);
});

Route::middleware(['auth','student'])->prefix('student')->group(function () {
    Route::get('/dashboard',[StudentDashboardController::class,'index'])->name('student.dashboard');
    Route::get('/my-courses', [StudentCourseController::class, 'index'])->name('student.courses');
    Route::get('/live-classes', [LiveClassController::class,'index']) ->name('student.live.classes');
});


Route::view('/', 'home')->name('home');
Route::view('/about', 'about');
Route::view('/courses', 'courses');
Route::view('/contact', 'contact');

// Route::get('/dashboard', function () {
//     return view('/admin/dashboard');
// })->middleware(['auth', 'verified'])->name('/admin/dashboard');






// Route::middleware('auth')->group(function () {
//     Route::view('/dashboard', 'dashboard')->name('/admin/dashboard');
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
Route::post('/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
Route::get('/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');


require __DIR__.'/auth.php';
