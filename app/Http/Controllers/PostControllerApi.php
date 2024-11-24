<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostControllerApi extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all posts with their relationships
        $posts = Post::with(['topic', 'author'])->get();

        return response()->json([
            'status' => 200,
            'data' => $posts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'required',
            'author_id' => 'required',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create a new post
        $post = Post::create($validator->validated());

        return response()->json([
            'status' => 201,
            'message' => 'Post created successfully.',
            'data' => $post,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Fetch a specific post with its relationships
        $post = Post::with(['topic', 'author'])->find($id);

        if (!$post) {
            return response()->json([
                'status' => 404,
                'message' => 'Post not found.',
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $post,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Fetch the post
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'status' => 404,
                'message' => 'Post not found.',
            ], 404);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'topic_id' => 'sometimes|exists:topics,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes',
            'author_id' => 'sometimes',
            'status' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update the post
        $post->update($validator->validated());

        return response()->json([
            'status' => 200,
            'message' => 'Post updated successfully.',
            'data' => $post,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Fetch the post
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'status' => 404,
                'message' => 'Post not found.',
            ], 404);
        }

        // Delete the post
        $post->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Post deleted successfully.',
        ]);
    }
}
