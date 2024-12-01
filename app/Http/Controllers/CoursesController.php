<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

use function Laravel\Prompts\error;

class CoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Courses::get();

        foreach ($courses as $course){
           $CourseData[]= [
            'id'=>$course->id,
            'title'=>$course->title,
            'description'=>$course->description,
            'images'=>$course->images,
            ];
            
        }

        
        

        return response()->json([
            'status' => 200,
            'data' => $CourseData,
        ]);
        
        
    }

    /**
     * Store a newly created resource in storage.
     */
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
        $imageName = time().'.'.$image->getClientOriginalExtension();
        $image->move(public_path('images'),$imageName);
        $data['image'] = $imageName;

        $validated["course_slug"] = Str::slug($validated['title']);
        $validated['image'] =  $imageName;
        $courses = Courses::create($validated);

        return response()->json([
            'message' => 'Course created successfully',
            'courses' => $courses,
        ], 201);
    }
    /**
     * Display the specified resource.
     */
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Debug: Check if the request data is received correctly
        if (empty($request->all())) {
            return response()->json([
                'status' => 400,
                'message' => 'No data received. Ensure you are sending data correctly.',
            ], 400);
        }

        // Retrieve the course by ID
        $course = Courses::findOrFail($id);

        // Initialize an array to hold validated fields
        $validatedData = [];

        // Validate the title if it's present in the request
        if ($request->has('title')) {
            $validator = Validator::make($request->only('title'), [
                'title' => 'required|string',
            ]);

            // If validation fails for the title
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->messages(),
                ], 422);
            }

            // Add validated title to the data array
            $request->input("title");
            $title = $request->input('title');
            $validatedData['title'] = $title;
            $validatedData['course_slug'] = Str::slug($title);
        }

        // Validate the description if it's present in the request
        if ($request->has('description')) {
            $validator = Validator::make($request->only('description'), [
                'description' => 'required|max:225',
            ]);

            // If validation fails for the description
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->messages(),
                ], 422);
            }

            // Add validated description to the data array
            $validatedData['description'] = $request->input('description');
        }

        // Validate the image if it's present in the request
        if ($request->has('image')) {
            // You can add validation logic for the image here if needed
            // For example, to validate image type or size
            $validator = Validator::make($request->only('image'), [
                'image' => 'required|image|max:2048',  // Example validation rule for image
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->messages(),
                ], 422);
            }

            // Add validated image to the data array
            $validatedData['image'] = $request->input('image');
        }

        // Validate the course_slug if it's present in the request
        if ($request->has('course_slug')) {
            $validator = Validator::make($request->only('course_slug'), [
                'course_slug' => 'required|string|unique:courses,course_slug,' . $course->id,
            ]);

            // If validation fails for the course_slug
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->messages(),
                ], 422);
            }

            // Add validated course_slug to the data array
            $validatedData['course_slug'] = $request->input('course_slug');
        }

        // Update only the fields that are present and validated
        if (!empty($validatedData)) {
            $course->update($validatedData);
        }

        // Return response
        return response()->json([
            'status' => 200,
            'message' => 'Course updated successfully',
            'data' => $course,
        ]);
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $courses = Courses::findOrFail($id);
        $courses->delete();

        return response()->json(['message' => 'Course deleted successfully.']);
    }
}
