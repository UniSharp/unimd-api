<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Auth\Access\AuthorizationException;
use App\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // validate inputs
        $this->validate($request, User::$createRules);
        // encrypt password
        $input = $request->all();
        $input['password'] = bcrypt($request->input('password'));
        // create user
        User::create($input);

        return response()->json([
            'message' => 'success'
        ]);
    }

    public function login(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');
        if (!$token = auth()->attempt($credentials)) {
            throw new AuthorizationException('Invalid credentials');
        }
        // return the token
        return response()->json(compact('token'));
    }

    public function logout()
    {
        auth()->logout();
        // return the token
        return response()->json([
            'message' => 'success'
        ]);
    }
}