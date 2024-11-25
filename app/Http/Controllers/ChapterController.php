<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
    $validator = Validator::make($request->all(), [
        'course_id' => 'required|exists:courses,id',
        'chapter_name' => 'required|string',
        'chapter_description' => 'required|string',
        'chapter_slug' => 'required|string|unique:chapters,chapter_slug', 
        'order' => 'required|integer', 
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'errors' => $validator->messages(),
        ], 422);
    }

    
    $validated = $validator->validated();

    
    $chapter = Chapter::create($validated);

    return response()->json([
        'message' => 'Chapter created successfully',
        'chapter' => $chapter,
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
    public function update(Request $request, $id)
    {
        if (empty($request->all())) {
            return response()->json([
                'status' => 400,
                'message' => 'No data received. Ensure you are sending data correctly.',
            ], 400);
        }
    
        // Retrieve the course by ID
        $chapter = Chapter::findOrFail($id);
    
        // Validate the request
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'chapter_name' => 'required|string',
            'chapter_description' => 'required|string',
            'chapter_slug' => 'required|string|unique:chapters,chapter_slug', 
            'order' => 'required|integer', 
        ]);
    
        // Check for validation errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ], 422);
        }
    
        // Update the course with validated data
        $chapter->update($validator->validated());
    
        return response()->json([
            'status' => 200,
            'message' => 'chapter updated successfully',
            'data' => $chapter,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $chapter = Chapter::findOrFail($id);
        $chapter->delete();

        return response()->json(['message' => 'Chapter deleted successfully.']);
    }
}
