<?php

use App\Http\Controllers\Api\AtomController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\MoleculeController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\V1\ServiceController as V1ServiceController;
use App\Http\Controllers\Api\V1\UserController as V1UserController;
use App\Http\Controllers\Api\V1\AtomController as V1AtomController;
use App\Http\Controllers\Api\V1\CategoryController as V1CategoryController;
use App\Http\Controllers\Api\V1\CourseController as V1CourseController;
use App\Http\Controllers\Api\V1\InvoiceController as V1InvoiceController;
use App\Http\Controllers\Api\V1\OrderController as V1OrderController;
use App\Http\Controllers\Api\V1\UserCourseController as V1UserCourseController;
use App\Http\Controllers\Api\V1\UserAtomController as V1UserAtomController;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::resource('/services', ServiceController::class);
Route::resource('/categories', CategoryController::class);
Route::resource('/courses', CourseController::class);
Route::resource('/molecules', MoleculeController::class);
Route::resource('/atoms', AtomController::class);
Route::get('/orders/{id}/approve', [OrderController::class, 'approve']);
Route::resource('/orders', OrderController::class);


Route::prefix('v1')->group(function () {
    Route::post("/register", [V1UserController::class, 'register']);
    Route::post("/login", [V1UserController::class, 'login']);
    Route::get("/activation", [V1UserController::class, 'activation']);
    Route::post("/forgot-password", [V1UserController::class, 'forgotPassword']);
    Route::post("/change-password", [V1UserController::class, 'changePassword']);

    Route::get('/categories', [V1CategoryController::class, 'index']);
    Route::get('/categories/{id}', [V1CategoryController::class, 'show']);
    Route::get('/services', [V1ServiceController::class, 'index']);
    Route::get('/services/{id}', [V1ServiceController::class, 'show']);
});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get("/users", function (Request $request) {
        $user = $request->user();
        $subscriber = Subscriber::where('user_id', $user->id)->first();
        $user->image = url(Storage::url('uploads/users/' . $user->image));
        $user->subscriber = $subscriber;

        if ($subscriber->expired_at != null && $subscriber->expired_at <= strtotime(date('Y-m-d H:i:s'))) {
            $subscriber->update([
                'status' => 'expired'
            ]);
        }

        $user->subscriber->active_at = date("Y-m-d H:i:s", $user->subscriber->active_at);
        $user->subscriber->expired_at = date("Y-m-d H:i:s", $user->subscriber->expired_at);

        return response()->json([
            'success' => true,
            'message' => 'data user',
            'data' => $user
        ]);
    });

    Route::put('/users/upload', [V1UserController::class, 'upload']);

    Route::post('/orders', [V1OrderController::class, 'store']);
    Route::get('/orders', [V1OrderController::class, 'index']);
    Route::get('/orders/{id}', [V1OrderController::class, 'show']);
    Route::put('/orders/{id}', [V1OrderController::class, 'upload']);
    Route::get('/invoices', [V1InvoiceController::class, 'index']);

    Route::get('/courses', [V1CourseController::class, 'index']);
    Route::get('/courses/{id}', [V1CourseController::class, 'show']);
    Route::get('/atoms/{id}/ordinal/{ordinal}', [V1AtomController::class, 'showByOrdinal']);
    Route::post('/user-courses', [V1UserCourseController::class, 'store']);
    Route::get('/user-courses', [V1UserCourseController::class, 'index']);
    Route::post('/user-atoms', [V1UserAtomController::class, 'store']);
    Route::post('/user-courses/certificate', [V1UserCourseController::class, 'certificate']);
});
