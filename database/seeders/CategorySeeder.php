<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $categories = [
      [ 'name_fr'=>'','name_ar' => '', 'name' => 'IRG', 'color' => '#FF0000'],
      [ 'name_fr'=>'','name_ar' => '', 'name' => 'CNAS', 'color' => '#00FF00'],
      [ 'name_fr'=>'','name_ar' => '', 'name' => 'CASNOS', 'color' => '#FFFF00'],
      [ 'name_fr'=>'','name_ar' => '', 'name' => 'CACOBATPH', 'color' => '#00FFFF']
    ];

    foreach ($categories as $cat) {
      Category::create($cat);
    }
  }
}
