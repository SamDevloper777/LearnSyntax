<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChapterController extends Controller
{
   
    public function index()
    {
        $chapter = Chapter::with("course")->get();
        return response()->json([
            'message' => 'chapter Fetched successfully',
            'chapters' => $chapter,
        ], 200);
    }

    
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'course_id' => 'required|exists:courses,id',
        'chapter_name' => 'required|string',
        'chapter_description' => 'required|string',
        'order' => 'required|integer', 
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'errors' => $validator->messages(),
        ], 422);
    }

    
    $validated = $validator->validated();
    
    $validated["chapter_slug"] = Str::slug($validated['chapter_name']);

    
    $chapter = Chapter::create($validated);

    return response()->json([
        'message' => 'Chapter created successfully',
        'chapter' => $chapter,
    ], 201);
}


    public function show(string $id)
    {
          
          $chapter = Chapter::with(['courses'])->find($id);

          if (!$chapter) {
              return response()->json([
                  'status' => 404,
                  'message' => 'Post not found.',
              ], 404);
          }
  
          return response()->json([
              'status' => 200,
              'data' => $chapter,
          ]);
    }

   
    public function update(Request $request, $id)
{
    // Check if any data was sent
    if (empty($request->all())) {
        return response()->json([
            'status' => 400,
            'message' => 'No data received. Ensure you are sending data correctly.',
        ], 400);
    }

    // Retrieve the chapter by ID
    $chapter = Chapter::findOrFail($id);

    // Initialize an array to hold the validated data
    $validatedData = [];

    // Validate and update the 'course_id' if it's present
    if ($request->has('course_id')) {
        $validator = Validator::make($request->only('course_id'), [
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ], 422);
        }

        // Add validated 'course_id' to the data array
        $validatedData['course_id'] = $request->input('course_id');
    }

    // Validate and update the 'chapter_name' if it's present
    if ($request->has('chapter_name')) {
        $validator = Validator::make($request->only('chapter_name'), [
            'chapter_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ], 422);
        }

        // Add validated 'chapter_name' to the data array
        $chapter_name = $request->input('chapter_name');
        $validatedData['chapter_name'] = $chapter_name;
        $validatedData['chapter_slug'] = Str::slug($chapter_name);
       
    }

    // Validate and update the 'chapter_description' if it's present
    if ($request->has('chapter_description')) {
        $validator = Validator::make($request->only('chapter_description'), [
            'chapter_description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ], 422);
        }

        // Add validated 'chapter_description' to the data array
        $validatedData['chapter_description'] = $request->input('chapter_description');
    }

   
   
    // Validate and update the 'order' if it's present
    if ($request->has('order')) {
        $validator = Validator::make($request->only('order'), [
            'order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ], 422);
        }

        // Add validated 'order' to the data array
        $validatedData['order'] = $request->input('order');
    }

    // If there's any data to update, update the chapter with validated data
    if (!empty($validatedData)) {
        $chapter->update($validatedData);
    }

    // Return the updated chapter as the response
    return response()->json([
        'status' => 200,
        'message' => 'Chapter updated successfully',
        'data' => $chapter,
    ]);
}

    public function destroy(string $id)
    {
        $chapter = Chapter::findOrFail($id);
        $chapter->delete();

        return response()->json(['message' => 'Chapter deleted successfully.']);
    }
}
