<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Helpers\Helper;

class UserTest extends TestCase
{
  public function test_user_can_see_his_profile(): void
  {
    $this->requestApiResource($this->base_url(), Helper::getResource(User::find(2)));
  }

  public function test_user_can_update_his_profile(): void
  {
    $data = [
      'name' => 'testing name',
      'email' => 'testing@email.com',
      'phone' => '0777121212',
      'password' => env('TEST_PASSWORD')
    ];
    $response = $this->actingAs(User::find(2))->putJson($this->base_url(), $data)->assertStatus(204);
  }

  public function test_user_can_update_his_password(): void
  {
    $data = [
      'current_password' => env('TEST_PASSWORD'),
      'password' => 'testtest2',
      'password_confirmation' => 'testtest2',
    ];
    $response = $this->actingAs(User::find(2))->putJson($this->base_url('/password'), $data)
      ->assertStatus(204);
  }
}
