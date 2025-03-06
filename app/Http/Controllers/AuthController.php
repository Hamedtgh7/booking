<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data=$request->validate([
            'name'=>'required|min:3|max:31',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:6|max:31',
            'role'=>'required|in:client,admin'
        ]);

        User::query()->create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>Hash::make($data['password']),
            'role'=>$data['role']
        ]);

        return response()->json(['message'=>'User registered succcessfully.'],201);
    }

    public function login(Request $request)
    {
        $data=$request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $user=User::query()->where('email',$data['email'])->first();

        if (!$user|| !Hash::check($data['password'],$user->password)){
            return response()->json(['message'=>'Invalid'],401);
        }

        $token=$user->createToken('Booking')->plainTextToken;

        return response()->json(['message'=>'Login successfully.','token'=>$token,'role'=>$user->role,'email'=>$user->email,'name'=>$user->name]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message'=>'Logout successfully.']);
    }
}
