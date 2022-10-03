<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Molecule;
use App\Models\Subscriber;
use App\Models\UserAtom;
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
                $molecules = Molecule::where('course_id', $userCourse->course_id)->withCount('atoms')->with(['atoms' => function ($query) use ($request) {
                    $query->select('id', 'molecule_id')->withCount(['userAtoms' => function ($query) use ($request) {
                        $query->where('user_id', $request->user()->id);
                    }]);
                }])->get();
                $atomsCount = 0;
                $userAtomsCount = 0;
                foreach ($molecules as $molecule) {
                    $atomsCount += $molecule->atoms_count;
                    foreach ($molecule->atoms as $atom) {
                        $userAtomsCount += $atom->user_atoms_count;
                    }
                }
                $userCourse->course->molecules = $molecules;
                $userCourse->atoms_count = $atomsCount;
                $userCourse->user_atoms_count = $userAtomsCount;
                $userCourse->total = ($userCourse->user_atoms_count / $userCourse->atoms_count) * 100;
                if ($userCourse->total === 100) {
                    $userCourse = UserCourse::where('user_id', $request->user()->id)->where('course_id', $userCourse->course_id)->first();
                    $userCourse->update([
                        'is_completed' => true,
                    ]);
                }
                $userCourse->total = number_format($userCourse->total, 2) . "%";
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
