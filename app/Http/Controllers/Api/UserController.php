<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return [
            'user' => $request->user()->only(['username', 'phone', 'email']),
        ];
    }

    function update(Request $request)
    {
        $updating = 0;

        $validator = Validator::make($request->all(), [
            'username' => 'string|max:255',
            'email' => 'string|email|max:255',
            'phone' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 401);
        }

        $user = $request->user();

        if ($request->email) {
            $checkUser = User::select('id','username','phone')->where('email', $request->email)->first();
            if ($checkUser){

                if($checkUser->id != $user->id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => ["email"=>["The email has already been taken."]]
                    ], 401);
                }
            }
        }

        if ($request->username) {
            $user->username = $request->username;
            $updating++;
        }
        if ($request->email) {
            $user->email = $request->email;
            $updating++;
        }
        if ($request->phone) {
            $user->phone = $request->phone;
            $updating++;
        }

        if ($updating) {
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => $updating ? 'Your profile has been updated.' : 'Nothing to update.'
        ], 200);
    }
}
