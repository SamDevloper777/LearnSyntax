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
        'chapter_slug'=> 'required',
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
    

    
    $chapter = Chapter::create([
        'course_id' => $validated['course_id'],
        'chapter_name' => $validated['chapter_name'],
        'chapter_description' => $validated['chapter_description'],
        'chapter_slug' =>Str::slug($validated['chapter_slug']),
        'order' => $validated['order'],  // Assuming 'order' is a numeric field in the database table. Replace this with the actual field name if it's different.
]);



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
        if (empty($request->all())) {
            return response()->json([
                'status' => 400,
                'message' => 'No data received. Ensure you are sending data correctly.',
            ], 400);
        }
    
        
        $chapter = Chapter::findOrFail($id);
    
      
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
    
        
        $chapter->update($validator->validated());
    
        return response()->json([
            'status' => 200,
            'message' => 'chapter updated successfully',
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
