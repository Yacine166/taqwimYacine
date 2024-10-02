<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\Organization;

class EventTest extends TestCase
{

  public function test_user_can_see_one_of_his_organizations_events(): void
  {
    $this->requestApiCollection(route('organization.event.index', [
      'organization' => 1,
      // 'date'=>Organization::find(1)->events->first()->pivot->deadline,
      // 'completed'=>true,
    ]), Helper::getCollection(Organization::find(1)->events));
  }

  public function test_user_can_complete_one_of_his_organizations_events(): void
  {
    $this->actingAs(User::find(2))->put(route('organization.event.update', ['organization' => Organization::find(1)->id, 'event' => Organization::find(1)->events()->first()->pivot->id]), ['completed' => true,])
      ->assertNoContent();
  }
}