<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Foreach_;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $topic = Topic::with("chapter")->get();
        return response()->json([
            'message' => 'topic Fetched successfully',
            'topics' => $topic,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator =Validator::make($request->all(),[
            'chapter_id' => 'required|exists:chapters,id',
            'topic_name' => 'required|string',
            'order' => 'integer',
            'topic_description' => 'nullable|string',
            'topic_slug' => 'required|string|unique:topics',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }
        $topic = Topic::create($validator->validated());

        return response()->json([
            'status' => 201,
            'message' => 'topic created successfully.',
            'data' => $topic,
        ]);
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
        // Fetch the post
        $topic = Topic::find($id);

        if (!$topic) {
            return response()->json([
                'status' => 404,
                'message' => 'Post not found.',
            ], 404);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'chapter_id' => 'required|exists:chapters,id',
            'topic_name' => 'required|string',
            'order' => 'integer',
            'topic_description' => 'nullable|string',
            'topic_slug' => 'required|string|unique:topics',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,

                
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update the post
        $topic->update($validator->validated());

        return response()->json([
            'status' => 200,
            'message' => 'Topic updated successfully.',
            'data' => $topic,
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $topic = Topic::findOrFail($id);
        $topic->delete();

        return response()->json(['message' => 'Topic deleted successfully.']);
    }
}
