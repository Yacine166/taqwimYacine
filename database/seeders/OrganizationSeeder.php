<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Parameter;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $org = Organization::factory()->create();
    $params = Parameter::all();
    $org->parameters()->sync([
      1=> ['option'=>'10-50'],
      2=>['option'=>'Real'],
      3=>['option'=> 'Affiliated'],
      4=>['option'=>'Physic'],
    ]);
    
    $org->categories()->attach(Category::pluck('id'));
    $org->attachEvents();

    // $orgs = Organization::factory(50)->create();
    // foreach ($orgs as $org) {
    // }
    // foreach ($params as $param) {
    //   $org->parameters()->sync([$param->id => ['option'  => fake()->randomElement($param->options )]], false);
    // }
  }
}
