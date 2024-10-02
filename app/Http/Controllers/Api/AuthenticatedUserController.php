<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Requests\Api\UpdatePasswordRequest;

class AuthenticatedUserController extends Controller
{

  public function show()
  {
    return (new UserResource(auth()->user()->load(['organizations.parameters'])))->response();
  }

  public function update(UpdateUserRequest $request)
  {
    $validated = $request->validated();
    /** @var User */
    $user = auth()->user();
    $user->update($validated);
    return response('', 204);
  }

  public function updatePassword(UpdatePasswordRequest $request): Response
  {
    $validated = $request->validated();
    /** @var User */
    $user = auth()->user();
    $user->update($validated);
    return response('', 204);
  }

  public function updateLanguage(Request $request): Response
  {
    $validated = $request->validate(['locale'    => ['string', Rule::in(['en', 'fr', 'ar']),],
    ]);
    /** @var User */
    $user = auth()->user();
    $user->update($validated);
    return response('', 204);
  }

  public function destroy(Request $request){
    $request->validate([
      'email'=>['email','required',Rule::in([auth()->user()->email])],
      'current_password' => ['required', 'current_password'],
    ]);

    User::where('email',$request->only('email'))->delete();
    return response('', 204);
  }
}
