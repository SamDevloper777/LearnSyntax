<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthorControllerApi extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all authors
        $authors = Author::all();

        return response()->json([
            'status' => 200,
            'data' => $authors,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'author_name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'email' => 'required|email|unique:authors,email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Handle image upload
        $data = $validator->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('authors', 'public');
        }

        // Create a new author
        $author = Author::create($data);

        return response()->json([
            'status' => 201,
            'message' => 'Author created successfully.',
            'data' => $author,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Fetch the author
        $author = Author::find($id);

        if (!$author) {
            return response()->json([
                'status' => 404,
                'message' => 'Author not found.',
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $author,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Fetch the author
        $author = Author::find($id);

        if (!$author) {
            return response()->json([
                'status' => 404,
                'message' => 'Author not found.',
            ], 404);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'author_name' => 'sometimes|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'email' => 'sometimes|email|unique:authors,email,' . $author->id,
            'password' => 'sometimes|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Handle image upload
        $data = $validator->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('authors', 'public');
        }

        // Update the author
        $author->update($data);

        return response()->json([
            'status' => 200,
            'message' => 'Author updated successfully.',
            'data' => $author,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Fetch the author
        $author = Author::find($id);

        if (!$author) {
            return response()->json([
                'status' => 404,
                'message' => 'Author not found.',
            ], 404);
        }

        // Delete the author
        $author->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Author deleted successfully.',
        ]);
    }
}
