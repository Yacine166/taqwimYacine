<?php

namespace Tests;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 */
abstract class TestCase extends BaseTestCase
{
  use CreatesApplication;
  use RefreshDatabase;
  protected $seed = true;

  /**
   * build the Api's base url
   * @return string
   * @param string
   */
  public function base_url(string $endpoint = ""): string
  {
    return "/api/me$endpoint";
  }

  /**
   * test getting an api resource
   * @method Tests/TestCase requestApiResource(string $route, JsonResource $resource) @return $this
   * @param string $route  endpoint's url
   * @param Illuminate\Http\Resources\Json\JsonResource $resource  expected api Resource
   */
  public function requestApiResource(string $route, JsonResource $resource)
  {
    $request  = Request::create($route, 'GET');
    $jsonResource = $resource->response($request)->getData(true);
    return  $this->actingAs(User::find(2))->getJson($route)
      ->assertStatus(200)
      ->assertJson($jsonResource);
  }

  /**
   * test getting a collection of api resources
   * @method Tests/TestCase requestApiCollection(string $route, Illuminate\Http\Resources\Json\ResourceCollection $resource) @return $this
   * @param string $route endpoint's url
   * @param Illuminate\Http\Resources\Json\ResourceCollection $collection expected api Resource collection
   */
  public function requestApiCollection(string $route, ResourceCollection $collection)
  {
    $request = Request::create($route, 'GET');
    $jsonResponse = $collection->response($request)->getData(true);
    return  $this->actingAs(User::find(2))->getJson($route)
      ->assertStatus(200)
      ->assertJson($jsonResponse);
  }
}
