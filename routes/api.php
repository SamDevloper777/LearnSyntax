<?php


use App\Http\Controllers\ChapterController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\TopicController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource("chapter", ChapterController::class);
Route::apiResource('courses',CoursesController::class);
Route::apiResource('topic', TopicController::class);

