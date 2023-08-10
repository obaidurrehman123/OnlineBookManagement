<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class ValidateRole
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            //dump($user);
            if ($user->role === 'admin') {
                return $next($request);
            }
            return response()->json(['success' => false, 'message' => 'You must be admin'], 401);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
    }
}