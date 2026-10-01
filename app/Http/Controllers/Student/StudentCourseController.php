<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
class StudentCourseController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $enrollments = $request->user()->enrollments()
            ->with(['course', 'order', 'payment'])
            ->where('status', 'active')
            ->latest('enrolled_at')
            ->get();

        if ($request->expectsJson()) {
            return response()->json($enrollments->map(fn ($enrollment) => [
                'course_id' => $enrollment->course_id,
                'course_name' => $enrollment->course->course_name,
                'image' => $enrollment->course->image,
                'description' => $enrollment->course->description,
                'purchase_amount' => $enrollment->order->amount,
                'enrollment_date' => $enrollment->enrolled_at,
                'payment_status' => $enrollment->payment->status,
                'course_access_status' => $enrollment->status,
            ]));
        }

        return view('student.my-courses', compact('enrollments'));
    }
}
