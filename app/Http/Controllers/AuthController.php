<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;

    private const LOGIN_DECAY_SECONDS = 60;

    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_LOGIN_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return response()->json([
                'message' => 'Too many login attempts. Please try again later.',
                'retry_after' => $seconds,
            ], 429);
        }

        $loginValue = $request->input('login');
        $loginType = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $loginValue,
            'password' => $request->input('password'),
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);

            // block login of user with status  "Inactive"
            if (Auth::user()->status == 'Inactive') {
                Auth::logout();

                return response()->json(['message' => 'It appears your account is inactive. Please contact the Admin.'], 403);
            }
            $request->session()->regenerate();

            return response()->json(['message' => 'Login successful'], 200);
        } else {
            RateLimiter::hit($throttleKey, self::LOGIN_DECAY_SECONDS);

            if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_LOGIN_ATTEMPTS)) {
                $seconds = RateLimiter::availableIn($throttleKey);

                return response()->json([
                    'message' => 'Too many login attempts. Please try again later.',
                    'retry_after' => $seconds,
                ], 429);
            }

            $attempts = RateLimiter::attempts($throttleKey);
            $remainingAttempts = max(self::MAX_LOGIN_ATTEMPTS - $attempts, 0);

            return response()->json([
                'message' => 'Invalid credentials',
                'remaining_attempts' => $remainingAttempts,
            ], 401);
        }
    }

    private function throttleKey(Request $request): string
    {
        return $this->throttleIdentifier($request).'|'.$request->ip();
    }

    private function throttleIdentifier(Request $request): string
    {
        $loginValue = Str::lower((string) $request->input('login', ''));

        if (filter_var($loginValue, FILTER_VALIDATE_EMAIL)) {
            return $loginValue;
        }

        $email = User::query()
            ->whereRaw('LOWER(username) = ?', [$loginValue])
            ->value('email');

        return Str::lower((string) ($email ?: $loginValue));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
