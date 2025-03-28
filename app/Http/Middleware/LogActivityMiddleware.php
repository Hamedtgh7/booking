<?php

namespace App\Http\Middleware;

use App\Models\Schedule;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\UserActivity;

class LogActivityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response=$next($request);

        $user_id=Auth::id();
        $url=$request->path();
        $method=$request->method();
        $requestData=$request->all();
        $responseStatus=$response->getStatusCode();
        $action=$this->getAction($request);
        $description=$this->getDescription($action,Auth::user());

        UserActivity::query()->create([
            'user_id'=>$user_id,
            'url'=>$url,
            'method'=>$method,
            'action'=>$action,
            'requestData'=>$requestData,
            'responseStatus'=>$responseStatus,
            'description'=>$description
        ]);

        return $response;
    }

    private function getAction(Request $request)
    {
        $route=$request->path();
        if (str_contains($route,'logout')) return 'logout';
        if (str_contains($route,'schedules')&&$request->isMethod('get')) return 'view-schedules';
        if (str_contains($route,'schedules')&&$request->isMethod('delete')) return 'delete-schedules';

        if (str_contains($route,'users')){
            return str_contains($route,'schedule') ? 'client-views-admin-schedule' : 'view-admins-list';
        }
      
        if (str_contains($route,'appointments')&&$request->isMethod('post')) return 'create-appointments';
        if (str_contains($route,'appointments')&&$request->isMethod('get')) {
            return Auth::user()->isAdmin() ? 'admin-view-appointments' : 'client-view-appointments';
        };
        if (str_contains($route,'appointments')&&$request->isMethod('put')) {
            return Auth::user()->isAdmin() ? 'admin-update-appointments' : 'client-update-appointments';
        };
        if (str_contains($route,'notifications')&&$request->isMethod('get')) return 'view-notifications';
        if (str_contains($route,'notifications')&&$request->isMethod('put')) return 'read-notifications';

        
        return 'unknown';
    }

    private function getDescription(string $action ,?User $user)
    {
        $username=$user->name;
        
        return match ($action){
            'logout'=>"$username has logout.",
            'view-schedules'=>"$username viewed own schedules",
            'delete-schedules' => "$username deleted a schedule",
            'client-views-admin-schedule'=>"$username seen admin schedule",
            'view-admins-list'=>"$username seen admins list",
            'create-appointments'=>"$username created an appointment",
            'admin-view-appointments'=>"$username viewed own apoointments",
            'client-view-appointments'=>"$username viewed own apoointments",
            'admin-update-appointments'=>"$username updated appointment",
            'client-update-appointments'=>"$username updated appointment",
            'view-notifications'=>"$username viewed own notifications",
            'read-notifications'=>"$username viewed a notification"
        };
    }

}
