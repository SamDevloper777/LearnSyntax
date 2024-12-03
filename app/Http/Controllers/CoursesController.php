<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use function Laravel\Prompts\error;

class CoursesController extends Controller
{

    public function index()
    {
        $courses = Courses::get();


        return response()->json([
            'status' => 200,
            'data' => $courses,
        ]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|max:225',
            'image' => 'required|image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ], 422);
        }


        $validated = $validator->validated();
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->storeAs('images', $imageName, 'public');
        $data['image'] =  'images/' . $imageName;

        $validated["course_slug"] = Str::slug($validated['title']);
        $validated['image'] =  $imageName;
        $courses = Courses::create($validated);

        return response()->json([
            'message' => 'Course created successfully',
            'courses' => $courses,
        ], 201);
    }

    public function show(string $id)
    {
        $courses = Courses::find($id);

        if (!$courses) {
            return response()->json([
                'status' => 404,
                'message' => 'Post not found.',
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $courses,
        ]);
    }


    public function update(Request $request, $id)
    {
        $course = Courses::find($id);

        if (!$course) {
            return response()->json([
                'status' => 404,
                'message' => 'Course not found.',
            ], 404);
        }

        $validatedData = [];


        if ($request->has('title')) {
            $validator = Validator::make($request->only('title'), [
                'title' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->messages(),
                ], 422);
            }

            $title = $request->input('title');
            $validatedData['title'] = $title;
            $validatedData['course_slug'] = Str::slug($title);
        }


        if ($request->has('description')) {
            $validator = Validator::make($request->only('description'), [
                'description' => 'required|max:225',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->messages(),
                ], 422);
            }

            $validatedData['description'] = $request->input('description');
        }


        if ($request->file('image')) {
            $validator = Validator::make($request->only('image'), [
                'image' => 'required|image|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->messages(),
                ], 422);
            }
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('images', $imageName, 'public');
            $validatedData['image'] =  'images/' . $imageName;
        }


        $course->update($validatedData);

        return response()->json([
            'status' => 200,
            'message' => 'Course updated successfully ',
            'data' => $course,
        ]);
    }
    public function destroy(string $id)
    {
        $courses = Courses::findOrFail($id);
        $courses->delete();

        return response()->json(['message' => 'Course deleted successfully.']);
    }
}
