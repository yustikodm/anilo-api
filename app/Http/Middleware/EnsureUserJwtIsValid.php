<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;

class EnsureUserJwtIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $key = config('jwt.key');
            $token = $request->bearerToken();

            $decode = JWT::decode($token, $key, ['HS256']);

        } catch (\Exception $e) {
            return response([
                'result' => 'FAILED',
                'message' => 'token provided is no valid'
            ]);
        }

        Auth::setUser(new User(
            [
                'username' => $decode->sub,
                'name' => $decode->name,
                'status' => $decode->status,
                'address' => $decode->address
            ]
        ));

        return $next($request);
    }
}
