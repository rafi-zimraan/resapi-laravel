<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\returnSelf;

class ApiAuthController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $data = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $data->createToken('AuthToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => $data,
            'token' => $token
        ], 200);
    }

    // login
    public function login(Request $request)
    {
        // dd('test');
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        if (auth()->attempt($request->only('email', 'password'))) {

            $token = auth()->user()->createToken('AuthToken')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'data' => auth()->user(),
            ], 401);
        } else {
            return response()->json([
                'meesage' => 'unauthorized'
            ]);
        }
    }

    // logout
    public function logout()
    {
        dd('ok');
        $data = auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'berhasil metu!!',
            'data' => $data
        ], 200);
    }
}
