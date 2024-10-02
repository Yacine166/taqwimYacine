<?php

namespace App\Http\Controllers\Api;

use App\Models\Parameter;
use App\Http\Controllers\Controller;
use App\Http\Resources\ParameterCollection;

class ParameterController extends Controller
{
  public function __invoke()
  {
    return (new ParameterCollection(Parameter::all()))->response();
  }
}
