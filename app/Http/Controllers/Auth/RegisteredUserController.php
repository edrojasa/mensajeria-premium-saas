<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\RegisterUserWithOrganization;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterUserRequest $request, RegisterUserWithOrganization $registerUser): RedirectResponse
    {
        $user = $registerUser(
            $request->validated('name'),
            $request->validated('email'),
            $request->validated('password'),
            $request->validated('organization_name'),
        );

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
