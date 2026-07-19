<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $courses = Course::latest()->paginate(3);
        // $courses = Course::latest()->get();
        return view('/admin/courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('/admin/courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'course_name' => 'required',
        'description' => 'required',
        'price' => 'required|numeric',
        'duration' => 'required|numeric',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'status' => 'required',
            ]);

            $image = null;

            if ($request->hasFile('image')) {

                $image = $request->file('image')->store('courses', 'public');

            }

            Course::create([
                'course_name' => $request->course_name,
                'description' => $request->description,
                'price' => $request->price,
                'duration' => $request->duration,
                'image' => $image,
                'status' => $request->status,
            ]);

            return redirect()->route('courses.index')
                            ->with('success', 'Course Added Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        return view('admin/courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
            $request->validate([
                'course_name'=>'required',
                'description'=>'required',
                'price'=>'required',
                'duration'=>'required',
                'status'=>'required',
            ]);

            if($request->hasFile('image'))
            {
                if($course->image)
                {
                    Storage::disk('public')->delete($course->image);
                }
                $course->image=$request->file('image')->store('courses','public');
            }

            $course->update([
                'course_name'=>$request->course_name,
                'description'=>$request->description,
                'price'=>$request->price,
                'duration'=>$request->duration,
                'status'=>$request->status,
                'image'=>$course->image

            ]);
            return redirect() ->route('courses.index')->with('success','Course Updated Successfully');
            }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        if ($course->image && Storage::disk('public')->exists($course->image)) {
            Storage::disk('public')->delete($course->image);
        }
        $course->delete();
        return redirect()->route('courses.index') ->with('success','Course Deleted Successfully');
    }
}
