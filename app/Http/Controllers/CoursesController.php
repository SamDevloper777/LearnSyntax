<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CoursesController extends Controller
{
    public function index()
    {
        $courses = Courses::all();

        return response()->json([
            'status' => 200,
            'data' => $courses,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:225',
            'image' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ], 422);
        }

        $validated = $validator->validated();

        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('images', $imageName, 'public');
            $validated['image'] = 'images/' . $imageName;
        }

       
        $validated['course_slug'] = Str::slug($validated['title']);

        $course = Courses::create($validated);

        return response()->json([
            'status' => 201,
            'message' => 'Course created successfully.',
            'data' => $course,
        ], 201);
    }

    public function show(string $slug)
    {
        $course = Courses::where('course_slug', $slug)->first();

        if (!$course) {
            return response()->json([
                'status' => 404,
                'message' => 'Course not found.',
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $course,
        ]);
    }

    public function update(Request $request, string $slug)
    {
        $course = Courses::where('course_slug', $slug)->first();

        if (!$course) {
            return response()->json([
                'status' => 404,
                'message' => 'Course not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string|max:225',
            'image' => 'sometimes|required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ], 422);
        }

        $validated = $validator->validated();

       
        if ($request->has('title')) {
            $validated['course_slug'] = Str::slug($validated['title']);
        }

        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('images', $imageName, 'public');
            $validated['image'] = 'images/' . $imageName;

           
            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }
        }

        $course->update($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Course updated successfully.',
            'data' => $course,
        ]);
    }

    public function destroy(string $slug)
    {
        $course = Courses::where('course_slug', $slug)->first();

        if (!$course) {
            return response()->json([
                'status' => 404,
                'message' => 'Course not found.',
            ], 404);
        }

        
        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }

        $course->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Course deleted successfully.',
        ]);
    }
}
