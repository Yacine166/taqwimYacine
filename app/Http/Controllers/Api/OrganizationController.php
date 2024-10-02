<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Models\Category;
use App\Models\Parameter;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\OrganizationCollection;
use App\Http\Requests\Api\ResourceOrganizationRequest;

class OrganizationController extends Controller
{
  public function __construct()
  {
    //auth policy OrganizationPolicy.php
    $this->authorizeResource(Organization::class, 'organization');
  }

  public function index()
  {
    $organizations=Organization::where('user_id',auth()->user()->id)->with('parameters')->get();
    return new OrganizationCollection($organizations);
  }

  public function show(Organization $organization)
  {
    return (new OrganizationResource($organization))->response();
  }

  public function store(ResourceOrganizationRequest $request)
  {
    $request->validated();

    DB::beginTransaction();
    $organization = Organization::create([
      'name'    => $request->name,
      'user_id' => auth()->user()->id,
    ]);

    $params = Parameter::all();
    $params_attach_args = [];
    foreach ($params as $param) {
      $params_attach_args[$param->id] = ['option' => $param->options[$request->input($param->slug)]];
    }

    $organization->parameters()->attach($params_attach_args);

    //add pivot for notif ability & days
    $organization->categories()->attach(Category::pluck('id'));

    //creating events for the organization
    $organization->attachEvents();
    DB::commit();
    return response()->json('', 201);
  }

  public function update(Organization $organization, ResourceOrganizationRequest $request)
  {
    DB::beginTransaction();

    $organization->update([
      'name' => $request->name,
    ]);
    $parameters=Parameter::all();
    $organization_param = $organization->parameters->load('events');
    $events=new \Illuminate\Database\Eloquent\Collection();
    $is_updated = false;


    foreach ($parameters as $index => $parameter) {
      if(! $organization_param->contains($parameter)){
        $organization->parameters()->attach([$parameter->id=>['option'=>$parameter->options[$request->input($parameter->slug)]]]);
        if(!$parameter->events->isEmpty()){
          $is_updated = true;
        }
      }else{
        $pivot = $organization_param->where('id',$parameter->id)->first()->pivot;
        $pivot->update(['option' => $parameter->options[$request->input($parameter->slug)]]);
        if ($pivot->wasChanged() && !$organization_param->where('id',$parameter->id)->first()->events->isEmpty()) {
          $is_updated = true;
          $events->push(...$organization_param->where('id',$parameter->id)->first()->events);
        }
      }

    }

    if ($is_updated) {
      $organization->attachEvents($events->load('parameters'),true);
    }

    DB::commit();
    return response('', 204);
  }

  public function destroy(Organization $organization)
  {
    $query = $organization->delete();
    return $query ? response("", 202) : abort(500, "something went wrong over here");
  }
}
