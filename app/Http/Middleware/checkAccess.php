<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class checkAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $result = Http::withHeaders([
            'Authorization' => 'Bearer '.$request['token'],
        ])->post('http://'.env("ACCOUNT_SERVICE_IP_ADDRESS").'/api/checkAccess');
        $user = $result->json();
        // dd($user);  
        if(array_key_exists("status",$user) && $user['status'] == 200) {
            $request['user_email'] = $user['user']['email'];
            $request['user_id'] = $user['user']['id'];
            $request['user_name'] = $user['user']['name'];
            return $next($request);
        } else {
            $message = ["message" => "Permission Denied"];
            return response($message, 401);
        }
        
    }
}
