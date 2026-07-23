<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LiveClass;

class LiveClassController extends Controller
{
    public function index()
    {
        $classes = LiveClass::with('course')
                    ->orderBy('class_date')
                    ->orderBy('start_time')
                    ->get();

        return view('student.live-classes', compact('classes'));
    }
}
