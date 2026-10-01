<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = Teacher::latest()->paginate(10);
        return view('admin.teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
        'name' => 'required|string|max:255',
        'designation' => 'required|string|max:255',
        'qualification' => 'required|string|max:255',
        'experience' => 'required|integer|min:0',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'about' => 'nullable|string',
        'status' => 'required|in:Active,Inactive',
        ]);

        $photo = null;

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('teachers', 'public');
        }

        Teacher::create([
            'name' => $request->name,
            'designation' => $request->designation,
            'qualification' => $request->qualification,
            'experience' => $request->experience,
            'about' => $request->about,
            'photo' => $photo,
            'status' => $request->status,
        ]);
        return redirect() ->route('teachers.index')->with('success', 'Teacher Added Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
         $request->validate([
            'name'=>'required',
            'designation'=>'required',
            'qualification'=>'required',
            'experience'=>'required|integer|min:0',
            'photo'=>'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'about'=>'nullable|string',
            'status'=>'required|in:Active,Inactive',
        ]);

        if($request->hasFile('photo'))
        {
            if($teacher->photo)
            {
                Storage::disk('public')->delete($teacher->photo);
            }
            $teacher->photo = $request->file('photo')->store('teachers','public');
        }
        $teacher->update([
            'name'=>$request->name,
            'designation'=>$request->designation,
            'qualification'=>$request->qualification,
            'experience'=>$request->experience,
            'about'=>$request->about,
            'status'=>$request->status,
            'photo'=>$teacher->photo,
        ]);
        return redirect()->route('teachers.index')->with('success','Teacher Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        if($teacher->photo && Storage::disk('public')->exists($teacher->photo))
        {
            Storage::disk('public')->delete($teacher->photo);
        }
           $teacher->delete();
        return redirect()->route('teachers.index')->with('success','Teacher Deleted Successfully');
    }
}
