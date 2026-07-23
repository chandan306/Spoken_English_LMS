<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;

class StudentCourseController extends Controller
{
    public function index()
    {
        // $courses = auth()->user()->courses;
        $courses = Course::all();
        return view('student.my-courses', compact('courses'));
  
    }
}
