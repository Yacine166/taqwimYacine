<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\App;

class RegisteredUserController extends Controller
{
  /**
   * Handle an incoming registration request.
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  public function store(Request $request): Response
  {
    $request->validate([
      'name'     => ['required', 'string', 'max:255'],
      'email'    => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
      'phone' => ['string','nullable'],
      'locale'    => ['string', Rule::in(['en', 'fr','ar']),],
      'password' => ['required', 'confirmed', Rules\Password::defaults()]
    ]);

    $user = User::create([
      'name'     => $request->name,
      'email'    => $request->email,
      'password' => Hash::make($request->password),
      'phone'    => $request->phone,
      'locale'    => $request->locale
    ]);

    event(new Registered($user));

    Auth::login($user);

    return response('',201);
  }

}
