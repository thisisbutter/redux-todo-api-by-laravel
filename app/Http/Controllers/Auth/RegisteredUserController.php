<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiToken;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
         
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
      
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ];
        event(new Registered($user));

        Auth::login($user);
       

        return response()->json($response);


        // return redirect(RouteServiceProvider::HOME);
    }

    public function logout(Request $request)
    {
        

        // auth()->user()->tokens()->delete();

        // return [
        //     'message' => 'Logged out'
        // ];
    }
}
