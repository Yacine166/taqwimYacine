<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateOrganizationCategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Models\Category;
use App\Models\Organization;
use App\Models\EventOrganization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrganizationCategoryController extends Controller
{
  public function index(Organization $organization)
  {
    Gate::authorize('view-organization', $organization);
    $categories = $organization->categories;
    return (new CategoryCollection($categories))->response();
  }

  public function update(Organization $organization, Category $category, UpdateOrganizationCategoryRequest $request)
  {
    DB::beginTransaction();
    $organization = $organization->load('categories');
    $validated = $request->validated();
    $is_notifable = $request->boolean('is_notifable');
    $days_before_notification = $request->days_before_notification;

    $organization_category = $organization->categories->find($category->id)->pivot;

    $organization_category->update([
      'days_before_notification' => $days_before_notification,
      'is_notifable' => $is_notifable
    ]);

    if ($organization_category->wasChanged('days_before_notification')) {
      EventOrganization::updateNotificationDates($organization, $category);
    }
    DB::commit();
    return response('', 204);
  }
}
