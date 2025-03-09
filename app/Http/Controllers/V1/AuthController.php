<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request):JsonResponse
    {
        $data=$request->validate([
            'name'=>'required|min:3|max:31',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:6|max:31',
        ]);

        User::query()->create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>Hash::make($data['password']),
            'role'=>'client'
        ]);

        return $this->successResponse('User registered successfully',[],Response::HTTP_CREATED);
    }

    public function login(Request $request):JsonResponse
    {
        $data=$request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $user=User::query()->where('email',$data['email'])->first();

        if (!$user|| !Hash::check($data['password'],$user->password)){
            return $this->errorResponse('Invalid credentials',[],Response::HTTP_UNAUTHORIZED);
        }

        $token=$user->createToken('Booking')->plainTextToken;

        return $this->successResponse('Login successfully',[
            'token'=>$token,
            'role'=>$user->role,
            'email'=>$user->email,
            'name'=>$user->name
        ],Response::HTTP_OK);
    }

    public function logout(Request $request):JsonResponse
    {
        $request->user()->tokens()->delete();

        return $this->successResponse('Logout successfully',[],Response::HTTP_OK);
    }
}
