<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\error;

class CoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Courses::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required|string',
            'description' => 'required|max:225',
            'image' => 'nullable', 
            'course_slug' => 'required|string|unique:courses,course_slug,' 
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'errors' => $validator->messages(),
        ], 422);
    }

    
    $validated = $validator->validated();

    
    $courses = Courses::create($validated);

    return response()->json([
        'message' => 'Course created successfully',
        'courses' => $courses,
    ], 201);
}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Courses $id)
    {
        return $request->all();
       
        $courses = Courses::findOrFail($id);
    
        // return $courses;
        $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'description' => 'required|max:225',
                'image' => 'nullable', 
                'course_slug' => 'required|string|unique:courses,course_slug,' 
        ]);
    
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ], 422);
        }
    
       
        $courses->update($validator->validated());
    
       
        return response()->json([
            'status' => 200,
            'message' => 'Course updated successfully',
            'data' => $courses,
        ]);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $courses = Courses::findOrFail($id);
        $courses->delete();

        return response()->json(['message' => 'Course deleted successfully.']);
    }
}
