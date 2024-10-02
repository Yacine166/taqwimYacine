<?php

namespace App\Http\Controllers;

use App\Models\BetaUser;
use Illuminate\Http\Request;

class BetaUserController extends Controller
{
  public function  __invoke(Request $request){
    $validated= $request->validate([
        "username"=>"required",
        "email"=>"required | email | unique:beta_users",
        "phone"=>"required | min:10 ",
        "organization_name"=>"required",
        "activity_sector"=>"required",
    ]);
    BetaUser::create($validated);
    return response('Account Registered successfully', 201);
  }
}