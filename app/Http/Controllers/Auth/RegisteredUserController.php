<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @param Request $request
     *
     * @return Model|Builder
     */
    public function store(Request $request): Model|Builder
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'gender' => ['required', 'in:0,1,2'],
        ]);

        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gender' => $request->gender
        ]);

        event(new Registered($user));

        Auth::login($user);

        return $user;
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function update(Request $request): mixed
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['sometimes', 'required', 'confirmed', Password::defaults()],
            'gender' => ['required', Rule::in([0, 1, 2])]
        ]);

        $user = $request->user();

        $user->update($request->only('name', 'gender'));

        if ($request->has('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return $user;
    }
}
