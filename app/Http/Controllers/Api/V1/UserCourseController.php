<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Molecule;
use App\Models\Subscriber;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PDF;

class UserCourseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->is_completed) {
            $userCourses = UserCourse::with(['course' => function ($query) {
                $query->select('id', 'name');
            }])->where('user_id', $request->user()->id)->where('is_completed', true)->get();
        } else {
            $userCourses = UserCourse::with(['course' => function ($query) {
                $query->select('id', 'name');
            }])->where('user_id', $request->user()->id)->get();

            foreach ($userCourses as $userCourse) {
                $molecules = Molecule::select('id')->where('course_id', $userCourse->course_id)->withCount('atoms')->get();
                $courseAtomsCount = 0;
                foreach ($molecules as $molecule) {
                    $courseAtomsCount += $molecule->atoms_count;
                }
                $userCourse->course->molecules = $molecules;
                $userCourse->course_atoms_count = $courseAtomsCount;
                if ($userCourse->progress === $userCourse->course_atoms_count && $userCourse->progress > 0) {
                    $userCourse = UserCourse::where('user_id', $request->user()->id)->where('course_id', $userCourse->course_id)->first();
                    $userCourse->update([
                        'is_completed' => true,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'data kursus',
            'data' => $userCourses
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        $findUserCourse = UserCourse::where('user_id', $request->user()->id)->where('course_id', $request->course_id)->first();

        if ($findUserCourse) {
            return response()->json([
                'success' => false,
                'message' => 'Selamat Belajar'
            ]);
        }

        $course = Course::where('id', $request->course_id)->first();
        $subscriber = Subscriber::where('user_id', $request->user()->id)->first();

        if ($course->is_subscriber && $subscriber->status === "expired") {
            return response()->json([
                'success' => false,
                'message' => 'anda belum berlangganan'
            ], 400);
        }

        $userCourse = UserCourse::create([
            'user_id' => $request->user()->id,
            'course_id' => $request->course_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Anda mengambil kelas ini',
            'data' => $userCourse
        ]);
    }

    public function certificate(Request $request)
    {
        $certificate = UserCourse::with(['user' => function ($query) {
            $query->select('id', 'name', 'email');
        }])->with(['course' => function ($query) {
            $query->select('id', 'name');
        }])->where('user_id', $request->user()->id)->where('course_id', $request->course_id)->where('is_completed', true)->first();

        $data = [
            'certificate' => $certificate
        ];

        $pdf = PDF::loadview('pdf.certificate', $data);
        return $pdf->download('certificate.pdf');
    }
}
