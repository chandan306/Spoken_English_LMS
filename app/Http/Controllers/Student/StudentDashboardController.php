<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LiveClass;

class StudentDashboardController extends Controller
{
    
    public function index()
    {
        $user = auth()->user();
        $courseCount = $user->enrollments()->where('status', 'active')->count();
        $liveClassCount = LiveClass::whereHas('course.users', fn ($query) => $query->whereKey($user->id))
            ->whereIn('status', ['Upcoming', 'Live'])
            ->count();
        $paymentTotal = $user->payments()->where('payment_status', 'paid')->sum('amount');
        return view('student.dashboard',[
            'courseCount' => $courseCount,
            'liveClassCount' => $liveClassCount,
            'paymentTotal' => $paymentTotal,
        ]);

         
    }
}
