<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use Illuminate\Http\Request;

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
        $data = $request->validate([
            'title'=> 'required|string',
            'description'=>'required|max:225',
            'image'=>'nullable',
            'course_slug' => 'required|string',
        ]);

        
        foreach ($data as $key => $field) {
            if (!$request->has($key)) {
                return response()->json(['error' => ucfirst(str_replace('_', ' ', $key)) . " is requrird, please insert this field"], 400);
            }
        }
        
        $courses=courses::create($data);
           
        return response()->json([
            'message' => 'Courses created successfully',
            'brands' => $courses
        ], 200);
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
    public function update(Request $request, string $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
