<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        // Validate incoming request
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);

        // Send back validation errors, if any
        if ($validation->fails()) {
            return response()->json([$validation->errors()], 202);
        }

        // Create a new user
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Generate the user token and send back response
        $token = $user->createToken("Laravel Personal Access Client")->accessToken;
        return response()->json(['user' => $user, 'token' => $token], 201);
    }


    public function login(Request $request)
    {

        // Validate incoming request
        $validation = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string|min:8'
        ]);

        // Send back validation errors, if any
        if ($validation->fails()) {
            return response()->json([$validation->errors()], 202);
        }

        // Authenticate the user
        if (!Auth::guard('web')->attempt($request->only(['email', 'password']))) {
            return response()->json(['error' => 'Unauthorized, invalid usename/password combination'], 401);
        }

        $user = Auth::guard('web')->user();
        $token = $user->createToken("Laravel Personal Access Client")->accessToken;
        return response()->json(['user' => $user, 'token' => $token], 200);

    }

    public function getUser()
    {
        return response()->json([ 'user' => Auth::user()], 200);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        return response()->json(["message" => "You have successfully logged out"], 200);
    }
}
