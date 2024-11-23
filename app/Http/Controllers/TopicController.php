<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Foreach_;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Topic::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'topic_name' => 'required|string',
            'order' => 'integer',
            'topic_description' => 'nullable|string',
            'topic_slug' => 'required|string|unique:topics',
        ]);
    
        $topic = Topic::create($validated);

        
        foreach ($validated as $field) {
            if (!$request->has($field)) {
                return response()->json(['error' => ucfirst(str_replace('_', ' ', $field)) . " is requrird, please insert this field"], 400);
            }
        }
    
        return response()->json([
            'message' => 'topic created successfully',
            'topic' => $topic
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
        $topic = Topic::findOrFail($id);

    $validated = $request->validate([
        'chapter_id' => 'exists:chapters,id',
        'topic_name' => 'string',
        'order' => 'integer',
        'topic_description' => 'string',
        'topic_slug' => 'string|unique:topics,topic_slug,' . $topic->id,
    ]);

    $topic->update($validated);

    foreach ($validated as $field) {
        if (!$request->has($field)) {
            return response()->json(['error' => ucfirst(str_replace('_', ' ', $field)) . " is requrird, please insert this field"], 400);
        }
    }
    
    return response()->json([
        'message' => 'topic updated successfully',
        'brands' => $topic
    ], 200);
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
