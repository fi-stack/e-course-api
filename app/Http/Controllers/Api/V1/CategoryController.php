<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::with('courses')->get();

        foreach ($categories as $category) {
            $category->image = url(Storage::url('uploads/categories/' . $category->image));
            foreach ($category->courses as $course) {
                $course->image = url(Storage::url('uploads/courses/' . $course->image));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'data kategori',
            'data' => $categories
        ]);
    }

    public function show(Request $request, $id)
    {
        $category = Category::where('id', $id)->with('courses')->first();

        $category->image = url(Storage::url('uploads/categories/' . $category->image));

        foreach ($category->courses as $course) {
            $course->image = url(Storage::url('uploads/courses/' . $course->image));
        }

        return response()->json([
            'success' => true,
            'message' => 'data kategori',
            'data' => $category
        ]);
    }
}
