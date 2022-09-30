<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::get();

        foreach ($categories as $category) {
            $category->image = url(Storage::url('uploads/categories/' . $category->image));
        }

        return response()->json([
            'success' => true,
            'message' => 'data kategori',
            'data' => $categories
        ]);
    }

    public function show(Request $request, $id)
    {
        $category = Category::where('id', $id)->first();

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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
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
            $imagePath = '/uploads/categories/' . $imageName;
            Storage::disk('public')->put($imagePath, base64_decode($image));

            $category = Category::create([
                'name' => $request->name,
                'image' => $imageName,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'data kategori berhasil dibuat',
                'data' => $category
            ]);
        } else {
            $category = Category::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'data kategori berhasil dibuat',
                'data' => $category
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        $category = Category::where('id', $id)->first();

        if ($request->image === null) {
            $category->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'kategori berhasil diubah',
                'data' => $category
            ]);
        } else {
            Storage::disk('public')->delete('/uploads/categories/' . $category->image);

            $image_64 = $request->image;
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
            $image = str_replace($replace, '', $image_64);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(10) . '.' . $extension;
            $imagePath = '/uploads/categories/' . $imageName;
            Storage::disk('public')->put($imagePath, base64_decode($image));

            $category->update([
                'name' => $request->name,
                'image' => $imageName,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'kategori berhasil diubah',
                'data' => $category
            ]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $category = Category::where('id', $id)->first();

        if ($request->image === null) {
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'kategori berhasil dihapus',
                'data' => $category
            ]);
        } else {
            Storage::disk('public')->delete('/uploads/categories/' . $category->image);

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'kategori berhasil dihapus',
                'data' => $category
            ]);
        }
    }
}
