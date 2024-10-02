<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\Parameter;
use App\Models\Organization;

class OrganizationTest extends TestCase
{
  public function test_user_can_see_his_organizations(): void
  {
    $this->RequestApiCollection(route('organization.index'),Helper::getCollection(Organization::all()));
  }

  public function test_user_can_see_one_of_his_organizations(): void
  {
    $this->requestApiResource(route('organization.show',['organization'=>1]),Helper::getResource(Organization::find(1)));
  }

  public function test_user_can_see_form_to_create_new_organization(): void
  {
    $this->requestApiCollection(route('parameter.index'),Helper::getCollection(Parameter::all()));
  }

  public function test_user_can_store_new_organization(): void
  {
    $response = $this->actingAs(User::find(2))->post(route('organization.store'), [
      'name' => fake()->company(),
      'activity_sector' => 'Production de biens',
      "employees_number" => "51-100",
      "tax_ability" => "Fixed",
      "cacobatph" => "Not Affiliated",
      "person" => "Physic"
    ]);
    $response->assertCreated();
  }

  public function test_user_can_update_one_of_his_organizations(): void
  {
    $response = $this->actingAs(User::find(2))->put(route('organization.update', ['organization' => 1]), [
      'name' => fake()->company(),
      'activity_sector' => 'Production de biens',
      "employees_number" => "51-100",
      "tax_ability" => "Fixed",
      "cacobatph" => "Not Affiliated",
      "person" => "Physic"
    ]);
    $response->assertNoContent();
  }

  public function test_user_can_delete_one_of_his_organizations(): void
  {
    $response = $this->actingAs(User::find(2))->delete(route('organization.destroy', ['organization' => 1]));
    $response->assertAccepted();
  }
}