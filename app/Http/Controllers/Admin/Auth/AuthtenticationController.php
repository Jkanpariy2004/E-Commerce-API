<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthtenticationController extends Controller
{
    public function Authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fix validation errors.',
                'error' => $validator->errors(),
            ], 400);
        } else {

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = User::find(Auth::user()->id);

                $token = $user->createToken('token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name,
                    ],
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Please Enter Valid Email & Password.',
                ], 400);
            }
        }
    }

    public function Logout()
    {
        $user = User::find(Auth::user()->id);

        if ($user) {
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout Successfully.',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User Not Found.',
            ], 404);
        }
    }
}
