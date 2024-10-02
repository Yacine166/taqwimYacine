<?php

namespace App\Http\Controllers\Api;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\EventOrganization;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;

class OrganizationEventController extends Controller
{

  public function index(Organization $organization, Request $request)
  {
    Gate::authorize("view-organization", $organization);

    $events = $organization->events()->with("category");

    if ($request->has("date") && Carbon::hasFormat($request->date, "Y-m-d"))
    $events = $events->wherePivot("deadline", $request->date);
    
    if ($request->has("month"))
    $events = $events->wherePivotBetween("deadline", [now()->month($request->month)->startOfMonth(), now()->month($request->month)->endOfMonth()]);
    
    if ($request->has("categories"))
      $events = $events->whereHas("category", function ($q) use ($request) {
        $q->whereIn("id", $request->categories);
      });

    if ($request->has("completed"))
      $events = $events->wherePivot("completed_at", $request->boolean("completed") ? "!=" : "=", null);

    if ($request->has("search")) $events = $events->where("name", "like", "%$request->search%");
    
    return new EventCollection($events->get());
  }

  public function update(Organization $organization, EventOrganization $event, Request $request)
  {
    Gate::authorize('update-organizationEvent', [$organization, $event]);
    $validated = $request->validate(
      ['completed' => ['required', 'boolean']]
    );
    DB::beginTransaction();
    $query = $request->boolean('completed') ? $event->update(['completed_at' => now()]) :   $event->update(['completed_at' => null]);
    DB::commit();
    return $query ? response('', 204) : abort(500);
  }

  public function show(Organization $organization,$event_id)
  {
    Gate::authorize("view-organization", $organization);

    $event = $organization->events()->wherePivot('event_organization.id',$event_id)->with("category");
  

    return new EventResource($event->first());
  }

  public function count(Organization $organization)
  {
    Gate::authorize("view-organization", $organization);
    $category_event_count = EventOrganization::where(['organization_id' => $organization->id, 'completed_at' => null])
    ->whereBetween("deadline", [now()->startOfMonth(), now()->endOfMonth()])
    ->join('events', 'events.id', '=', 'event_organization.event_id')
    ->join('categories', 'categories.id', '=', 'events.category_id')
    ->groupBy('categories.id')
    ->select(['categories.*', DB::raw("COUNT(event_organization.id) as events_count")])
    ->get();

    return new CategoryCollection($category_event_count);
  }
}
