<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Mail\AccountVerificationMail;
use App\Mail\ForgotPasswordMail;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet);

        $token = "";
        for ($i = 0; $i < 25; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'token' => $token
        ];

        Mail::to($request->email)->send(new AccountVerificationMail($data));

        if (Mail::failures()) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Please try again latter'
            ], 400);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'token' => $token
            ]);

            Subscriber::create([
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'register sukses',
                'data' => $user
            ]);
        }
    }

    public function activation(Request $request)
    {
        $user = User::where('token', $request->token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'wrong token'
            ], 400);
        }

        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet);

        $token = "";
        for ($i = 0; $i < 25; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        $user->update([
            'email_verified_at' => date('Y-m-d H:i:s'),
            'token' => $token
        ]);

        return response()->json([
            'success' => true,
            'message' => 'aktivasi berhasil'
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'email atau password salah'
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at === null) {
            return response()->json([
                'success' => false,
                'message' => 'belum melakukan aktivasi akun'
            ], 400);
        }

        $token = $user->createToken($user->id)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'login berhasil',
            'data' => $token
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'email tidak terdaftar'
            ], 400);
        }

        if ($user->email_verified_at === null) {
            return response()->json([
                'success' => false,
                'message' => 'belum melakukan aktivasi akun cek email',
            ], 400);
        }

        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet);

        $token = "";
        for ($i = 0; $i < 25; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'token' => $token
        ];

        Mail::to($request->email)->send(new ForgotPasswordMail($data));

        if (Mail::failures()) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Please try again latter'
            ], 400);
        } else {
            $user->update(['token' => $token]);

            return response()->json([
                'success' => true,
                'message' => 'lupa password terkirim',
                'data' => $user
            ]);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        $user = User::where('token', $request->token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'wrong token'
            ], 400);
        }

        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet);

        $token = "";
        for ($i = 0; $i < 25; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        $user->update([
            'password' => Hash::make($request->password),
            'token' => $token
        ]);

        return response()->json([
            'success' => true,
            'message' => 'password berhasil diubah',
            'data' => $user
        ]);
    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        $user = User::where('id', $request->user()->id)->first();

        if (!$user->image) {
            $image_64 = $request->image;
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
            $image = str_replace($replace, '', $image_64);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(10) . '.' . $extension;
            $imagePath = '/uploads/users/' . $imageName;
            Storage::disk('public')->put($imagePath, base64_decode($image));
            $user->update([
                'image' => $imageName
            ]);
        } else {
            Storage::disk('public')->delete('/uploads/users/' . $user->image);

            $image_64 = $request->image;
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
            $image = str_replace($replace, '', $image_64);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(10) . '.' . $extension;
            $imagePath = '/uploads/users/' . $imageName;
            Storage::disk('public')->put($imagePath, base64_decode($image));
            $user->update([
                'image' => $imageName
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'upload image berhasil',
            'data' => $user
        ]);
    }
}
