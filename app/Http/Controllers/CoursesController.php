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
            'course_slug' => 'required|string|unique:courses',
        ]

        );


        try{
            $course = new Courses();
            $course->title=$request->title;
            $course->description= $request->description;
           
            $courses->save();
           
        }
        $courses = Courses::create($data);

        return response()->json([
            'message' => 'Product created successfully',
            'brands' => $brands
        ], 200);
    } catch(){
        return response(\Exeption $e)->json([
            'message' => 'Product created successfully',
            'error' => $e->getMessage()->fe4
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
