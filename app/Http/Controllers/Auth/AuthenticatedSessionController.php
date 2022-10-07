<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param LoginRequest $request
     *
     * @return mixed
     * @throws ValidationException
     */
    public function store(LoginRequest $request): mixed
    {
        $request->authenticate();
        $request->session()->regenerate();

        return $request->user();
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
