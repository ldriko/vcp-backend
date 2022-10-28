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
use Illuminate\Support\Facades\Storage;
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed', Password::defaults(),
            'gender' => 'required|in:0,1,2',
            'picture' => 'sometimes|image'
        ]);

        /** @var User $user */
        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gender' => $request->gender
        ]);

        if ($request->hasFile('picture')) {
            $path = Storage::disk('users')->put('', $request->file('picture'));
            $user->update(['picture_path' => $path]);
        }

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
            'picture' => 'sometimes|image'
        ]);

        $user = $request->user();

        $user->update($request->only('name'));

        if ($request->hasFile('picture')) {
            $path = Storage::disk('users')->put('', $request->file('picture'));
            $user->update(['picture_path' => $path]);
        }

        return $user;
    }
}
