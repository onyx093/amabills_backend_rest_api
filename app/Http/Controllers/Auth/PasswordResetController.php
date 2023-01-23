<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordReset;
use App\Http\Requests\PasswordResetRequest;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    public function resetPassword(PasswordResetRequest $request)
    {

        // Check if token is valid
        $token = $request->input('token');

        if(!$passwordReset = PasswordReset::where('token', $token)->first())
        {
            return response()->json(["message" => "Invalid token"], 400);
        }

        // Check if user exists
        if(!$user = User::where('email', $passwordReset->email)->first())
        {
            return response()->json(["message" => "User doesn't exist"], 404);
        }

        // Save the new user's password
        $user->password = Hash::make($request->input('password'));
        $user->save();

        //Delete password reset entry
        PasswordReset::where('token', $token)->delete();

        return response()->json(["message" => "Success"]);
    }
}
