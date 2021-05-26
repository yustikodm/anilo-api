<?php


namespace App\Http\Controllers;


use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private $user = [
        'username' => 'yustikodm',
        # password is "password" with bcrypt
        'password' => '$2y$10$Lzxa3sBOs7/6TFTwsOSgI.C5ijCZYFFk7ll8ihOawLnmpV5v/908G',
        'name' => 'Yustiko',
        'full_name' => 'Yustiko Daru Murti',
        'status' => 1,
        'address' => 'Dukuh Pakis gg Masjid no 9A Surabaya'
    ];

    public function auth(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($request->input('username') == $this->user['username']
            && Hash::check($request->input('password'), $this->user['password'])) {

            $issuedAt = now();
            $key = config('jwt.key');
            $payload = [
                'iss' => 'https://example.org',
                'aud' => 'https://example.com',
                'iat' => $issuedAt->timestamp,
                'exp' => $expiredAt = $issuedAt->clone()->addHours(6)->timestamp,
                'sub' => $this->user['username'],
                'name' => $this->user['name'],
                'full_name' => $this->user['full_name'],
                'status' => $this->user['status'],
                'address' => $this->user['address'],
            ];

            $jwt = JWT::encode($payload, $key);

            return response([
                'result' => 'OK',
                'access_token' => $jwt,
                'token_type' => 'bearer',
                'expire_in' => $expiredAt,
                'refresh_ttl' => Carbon::createFromTimestamp($expiredAt)->addDays(12)->timestamp
            ]);

        } else {
            return response([
                'result' => 'FAILED',
                'message' => 'username or password not valid'
            ]);
        }
    }

    public function profile(Request $request)
    {
        $result = $request->user()->toArray();
        $result['result'] = 'OK';

        return response($result);
    }
}
