<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       return response()->json(data:Chapter::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'required|exists:course_id',
            'chapter_name' =>'required|string',
            'chapter_description' =>'required|string',
            'chapter_slug'=>'required|string|unique:chapters',
            'order'=>'required',
        ]);
       
        foreach ($data as $key => $field) {
            if (!$request->has($key)) {
                return response()->json(['$chapters' => ucfirst(str_replace('_', ' ', $key)) . 
                " is required, please insert this field"], 400);
            }
        }
        $chapters = Chapter::create($data);
       

        return response()->json([
            'message' => 'Chapter created successfully',
            'chapters' => $chapters
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
        $chapters = Chapter::findOrFail($id);

        $data = $request->validate([
            'course_id' => 'required|exists:course_id',
            'chapter_name' => 'required|string',
            'chapter_description' => 'required|string',
            'chapter_slug' => 'required|string|unique:chapters,chapter_slug',
            'order' => 'integer',
        ]);

        $chapters->update($data);

        foreach ($data as $key => $field) {
            if (!$request->has ($key)) {
                return response()->json(['$chapters' => ucfirst(str_replace('_', ' ', $key)) .
                    " is requrird, please insert this field"], 400);
            }
        }
        return response()->json([
            'message' => 'Chapter updated successfully',
            'chapters' => $chapters
        ], 200);

    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
