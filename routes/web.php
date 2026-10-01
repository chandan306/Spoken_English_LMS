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


Route::middleware(['auth', 'admin'])->post('/pdf-upload', [PdfController::class, 'upload'])->name('pdf.upload');

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::middleware(['auth','admin'])->prefix('admin')->group(function () {

//     Route::resource('teachers', TeacherController::class);

// });

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('student.dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('courses', CourseController::class)->except(['show']);
    Route::resource('teachers', TeacherController::class)->except(['show']);
    Route::view('pdf-upload', 'pdf')->name('pdf.form');
});

Route::middleware(['auth','student'])->prefix('student')->group(function () {
    Route::get('/dashboard',[StudentDashboardController::class,'index'])->name('student.dashboard');
    Route::get('/my-courses', [StudentCourseController::class, 'index'])->name('student.courses');
    Route::get('/live-classes', [LiveClassController::class,'index']) ->name('student.live.classes');
});


Route::get('/', [CourseController::class, 'home'])->name('home');
Route::view('/about', 'about');
Route::get('/courses', [CourseController::class, 'catalog'])->name('courses.catalog');
Route::get('/courses/{course}', [CourseController::class, 'details'])->name('courses.details');
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');
Route::view('/contact', 'contact');

// Route::get('/dashboard', function () {
//     return view('/admin/dashboard');
// })->middleware(['auth', 'verified'])->name('/admin/dashboard');

// Route::middleware('auth')->group(function () {
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'student'])->group(function () {
    Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::post('/checkout/{course}', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/success/{order}', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/failed/{order}', [PaymentController::class, 'failed'])->name('payment.failed');
    Route::get('/invoices/{invoice}/download', [App\Http\Controllers\InvoiceController::class, 'download'])->name('invoices.download');
    Route::get('/my-courses', [StudentCourseController::class, 'index'])->name('my-courses');
});


require __DIR__.'/auth.php';
