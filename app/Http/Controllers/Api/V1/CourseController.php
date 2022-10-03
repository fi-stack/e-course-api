<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $course = Course::where('id', $id)->select('id')
            ->with(['userCourses' => function ($query) use ($request) {
                $query->select('id', 'user_id', 'course_id')->where('user_id', $request->user()->id)->get();
            }])
            ->with(['molecules' => function ($query) use ($request) {
                $query->select('id', 'course_id', 'ordinal', 'name')
                    ->with(['atoms' => function ($query) use ($request) {
                        $query->select('id', 'ordinal', 'title', 'molecule_id')
                            ->with(['userAtoms' => function ($query) use ($request) {
                                $query->select('id', 'user_id', 'atom_id')->where('user_id', $request->user()->id)->get();
                            }]);
                    }]);
            }])->first();

        $course->image = url(Storage::url('uploads/courses/' . $course->image));

        return response()->json([
            'success' => true,
            'message' => 'data kursus',
            'data' => $course
        ]);
    }
}
