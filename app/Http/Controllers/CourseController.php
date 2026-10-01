<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function catalog()
    {
        $courses = Course::where('status', 'Active')->latest()->get();

        return view('courses', compact('courses'));
    }

    public function details(Course $course)
    {
        abort_unless($course->status === 'Active', 404);
        $isEnrolled = auth()->check() && $course->enrollments()
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->exists();

        return view('course-details', compact('course', 'isEnrolled'));
    }

    public function home()
    {
        $courses = Course::where('status', 'Active')->latest()->get();

        return view('home', compact('courses'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $courses = Course::latest()->paginate(3);
        // $courses = Course::latest()->get();
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'course_name' => 'required|string|max:255',
        'description' => 'required|string',
        'price' => 'required|numeric|min:0.01',
        'discount_price' => 'nullable|numeric|min:0.01|lt:price',
        'duration' => 'required|integer|min:1',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'status' => 'required|in:Active,Inactive',
            ]);

            $image = null;

            if ($request->hasFile('image')) {

                $image = $request->file('image')->store('courses', 'public');

            }

            Course::create([
                'course_name' => $request->course_name,
                'slug' => $this->uniqueSlug($request->course_name),
                'description' => $request->description,
                'price' => $request->price,
                'discount_price' => $request->input('discount_price'),
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
        return view('admin.courses.show', compact('course'));
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
                'course_name'=>'required|string|max:255',
                'description'=>'required|string',
                'price'=>'required|numeric|min:0.01',
                'discount_price'=>'nullable|numeric|min:0.01|lt:price',
                'duration'=>'required|integer|min:1',
                'image'=>'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'status'=>'required|in:Active,Inactive',
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
                'slug'=>$this->uniqueSlug($request->course_name, $course->id),
                'description'=>$request->description,
                'price'=>$request->price,
                'discount_price'=>$request->input('discount_price'),
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
        try {
            $course->delete();
        } catch (QueryException $exception) {
            return redirect()->route('courses.index')
                ->withErrors(['course' => 'Courses with existing orders cannot be deleted. Mark this course inactive instead.']);
        }

        if ($course->image && Storage::disk('public')->exists($course->image)) {
            Storage::disk('public')->delete($course->image);
        }

        return redirect()->route('courses.index') ->with('success','Course Deleted Successfully');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'course';
        $slug = $base;
        $suffix = 2;

        while (Course::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
