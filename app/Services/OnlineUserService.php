<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class OnlineUserService
{
    protected static string $CacheKey='online_users';

    public static function add(User $user):void
    {
        $users=Cache::get(self::$CacheKey,[]);
        if (!collect($users)->contains('id',$user->id)){
            $users[]=[
                'id'=>$user->id,
                'name'=>$user->name
            ];
        }

        Cache::put(self::$CacheKey,$users,now()->addHours(4));
    }

    public static function remove(User $user):void
    {
        $users=Cache::get(self::$CacheKey,[]);
        $users=collect($users)->reject(fn($u)=>$u['id']===$user->id)->values()->all();
        Cache::put(self::$CacheKey,$users,now()->addHours(4));
    }

    public static function all():array
    {
        return Cache::get(self::$CacheKey,[]);
    }
}