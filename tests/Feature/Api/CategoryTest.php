<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\Organization;

class CategoryTest extends TestCase
{
  public function test_user_can_see_one_of_his_organizations_categories_notification_settings(): void
  {
    $this->requestApiCollection(route('organization.category.index', ['organization' => 1]), Helper::getCollection(Organization::find(1)->categories));
  }

  public function
  test_user_can_update_one_of_his_organizationscategories_notification_settings(): void
  {
    $organization = Organization::find(1);
    $days_before_notification = $organization->categories->first()->pivot->days_before_notification;

    $this->actingAs(User::find(2))->put(
      route('organization.category.update', [
        'organization' => $organization->id,
        'category' => 1
      ]),
      ['is_notifable' => false, 'days_before_notification' => $days_before_notification]
    )
      ->assertNoContent();
  }
}
