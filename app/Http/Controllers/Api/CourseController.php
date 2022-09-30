<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::with('category')->get();

        foreach ($courses as $course) {
            $course->image = url(Storage::url('uploads/courses/' . $course->image));
        }

        return response()->json([
            'success' => true,
            'message' => 'data kursus',
            'data' => $courses
        ]);
    }

    public function show(Request $request, $id)
    {
        $course = Course::where('id', $id)->with('category')->first();

        $course->image = url(Storage::url('uploads/courses/' . $course->image));

        return response()->json([
            'success' => true,
            'message' => 'data kursus',
            'data' => $course
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'ordinal' => 'required',
            'category_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        if ($request->image) {
            $image_64 = $request->image;
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
            $image = str_replace($replace, '', $image_64);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(10) . '.' . $extension;
            $imagePath = '/uploads/courses/' . $imageName;
            Storage::disk('public')->put($imagePath, base64_decode($image));

            $course = Course::create([
                'name' => $request->name,
                'ordinal' => $request->ordinal,
                'image' => $imageName,
                'category_id' => $request->category_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'data kursus berhasil dibuat',
                'data' => $course
            ]);
        } else {
            $course = Course::create([
                'name' => $request->name,
                'ordinal' => $request->ordinal,
                'category_id' => $request->category_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'data kursus berhasil dibuat',
                'data' => $course
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'ordinal' => 'required',
            'category_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        $course = Course::where('id', $id)->first();

        if ($request->image === null) {
            $course->update([
                'name' => $request->name,
                'ordinal' => $request->ordinal,
                'category_id' => $request->category_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'kursus berhasil diubah',
                'data' => $course
            ]);
        } else {
            Storage::disk('public')->delete('/uploads/courses/' . $course->image);

            $image_64 = $request->image;
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
            $image = str_replace($replace, '', $image_64);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(10) . '.' . $extension;
            $imagePath = '/uploads/courses/' . $imageName;
            Storage::disk('public')->put($imagePath, base64_decode($image));

            $course->update([
                'name' => $request->name,
                'ordinal' => $request->ordinal,
                'image' => $imageName,
                'category_id' => $request->category_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'kursus berhasil diubah',
                'data' => $course
            ]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $course = Course::where('id', $id)->first();

        if ($request->image === null) {
            $course->delete();

            return response()->json([
                'success' => true,
                'message' => 'kursus berhasil dihapus',
                'data' => $course
            ]);
        } else {
            Storage::disk('public')->delete('/uploads/courses/' . $course->image);

            $course->delete();

            return response()->json([
                'success' => true,
                'message' => 'kursus berhasil dihapus',
                'data' => $course
            ]);
        }
    }
}
