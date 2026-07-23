<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    
    public function index()
    {
        // return "chandan sharma";
        return view('student.dashboard',[
            'courseCount'      => 5,
            'lessonCount'      => 22,
            'certificateCount' => 2,
            'paymentTotal'     => 4999,
        ]);

         
    }
}
