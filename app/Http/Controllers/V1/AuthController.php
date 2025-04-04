<?php

namespace App\Http\Controllers\V1;

use App\Events\UserOnline;
use App\Http\Controllers\Controller;
use App\Jobs\LogActivityJob;
use App\Models\LoginAttempt;
use App\Models\User;
use App\Models\UserActivity;
use App\Notifications\SuspisiousNotification;
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

        $user=User::query()->create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>Hash::make($data['password']),
            'role'=>'client'
        ]);

        $user_id=$user->id;
        $url=$request->path();
        $method=$request->method();
        $requestData=$request->all();
        $responseStatus=201;
        $action='register';
        $description="$user->name has registered";

        UserActivity::query()->create([
            'user_id'=>$user_id,
            'url'=>$url,
            'method'=>$method,
            'action'=>$action,
            'requestData'=>$requestData,
            'responseStatus'=>$responseStatus,
            'description'=>$description
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

        $ip=$request->ip();

        LoginAttempt::query()->create([
            'user_id'=>$user->id,
            'ip'=>$ip
        ]);

        if ($this->isSuspicious($user,$ip)){
            $user->notify(new SuspisiousNotification($user,$ip));
        }

        $user_id=$user->id;
        $url=$request->path();
        $method=$request->method();
        $requestData=$request->all();
        $responseStatus=200;
        $action='login';
        $description="$user->name has logined";

        $token=$user->createToken('Booking')->plainTextToken;

        UserActivity::query()->create([
            'user_id'=>$user_id,
            'url'=>$url,
            'method'=>$method,
            'action'=>$action,
            'requestData'=>$requestData,
            'responseStatus'=>$responseStatus,
            'description'=>$description
        ]);

        // broadcast(new UserOnline($user));

        return $this->successResponse('Login successfully',[
            'id'=>$user_id,
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

    public function isSuspicious($user,$ip)
    {
        $lastIp=LoginAttempt::query()->where('user_id',$user->id)->pluck('id')->toArray();

        return !in_array($ip,$lastIp);
    }
}
