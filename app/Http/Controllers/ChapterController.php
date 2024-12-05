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
            'course_id' => 'required',
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
        //$chapter_slug = Str::slug($request->chapter_name);

        $validated["chapter_slug"] = Str::slug($validated['chapter_name']);


        $chapter = Chapter::create($validated);



        return response()->json([
            'message' => 'Chapter created successfully',
            'chapter' => $chapter,
        ], 201);
    }


    public function show(string $slug)
    {
        $chapter = Chapter::where(['course'])->where('chapter_slug', $slug)->first();

        if (!$chapter) {
            return response()->json([
                'status' => 404,
                'message' => 'Chapter not found.',
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $chapter,
        ]);
    }



    public function update(Request $request, string $slug)
    {
        if (empty($request->all())) {
            return response()->json([
                'status' => 400,
                'message' => 'No data received. Ensure you are sending data correctly.',
            ], 400);
        }

        $chapter = Chapter::where('chapter_slug', $slug)->firstOrFail();

        $validatedData = [];

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

            $validatedData['course_id'] = $request->input('course_id');
        }

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

            $chapter_name = $request->input('chapter_name');
            $validatedData['chapter_name'] = $chapter_name;
            $validatedData['chapter_slug'] = Str::slug($chapter_name);
        }

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

            $validatedData['chapter_description'] = $request->input('chapter_description');
        }

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

            $validatedData['order'] = $request->input('order');
        }

        if (!empty($validatedData)) {
            $chapter->update($validatedData);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Chapter updated successfully',
            'data' => $chapter,
        ]);
    }


    public function destroy(string $slug)
    {
        $chapter = Chapter::where('chapter_slug', $slug)->firstOrFail();
        $chapter->delete();

        return response()->json(['message' => 'Chapter deleted successfully.']);
    }
}
