<?php

namespace Database\Seeders;

use App\Enums\Person;
use App\Enums\Cacobatph;
use App\Enums\TaxAbility;
use App\Models\Parameter;
use App\Enums\ActivitySector;
use App\Enums\EmployeesNumber;
use Illuminate\Database\Seeder;

class ParameterSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $param1 = Parameter::create([
      'name'       => 'number of employees',
      'slug'       => 'employees_number',
      'options'    => EmployeesNumber::cases(),
      'name_fr'    => 'lorem ipsum fr',
      'name_ar'    => 'lorem ipsum ar',
      'options_fr' =>  EmployeesNumber::cases(),
      'options_ar' =>  EmployeesNumber::cases(),
    ]);

    $param2 = Parameter::create([
      'name'       => 'tax ability',
      'slug'       => 'tax_ability',
      'options'    => TaxAbility::cases(),
      'name_fr'    => 'lorem ipsum fr',
      'name_ar'    => 'lorem ipsum ar',
      'options_fr' =>  TaxAbility::cases(),
      'options_ar' =>  TaxAbility::cases(),
    ]);

    $param3 = Parameter::create([
      'name'       => 'cacobatph',
      'slug'       => 'cacobatph',
      'options'    => Cacobatph::cases(),
      'name_fr'    => 'lorem ipsum fr',
      'name_ar'    => 'lorem ipsum ar',
      'options_fr' =>  Cacobatph::cases(),
      'options_ar' =>  Cacobatph::cases(),
    ]);

    $param3 = Parameter::create([
      'name'       => 'person',
      'slug'       => 'person',
      'options'    => Person::cases(),
      'name_fr'    => 'lorem ipsum fr',
      'name_ar'    => 'lorem ipsum ar',
      'options_fr' =>  Person::cases(),
      'options_ar' =>  Person::cases(),
    ]);

    $param3 = Parameter::create([
      'name'       => 'activity sector',
      'slug'       => 'activity_sector',
      'options'    =>  ActivitySector::cases(),
      'name_fr'    => 'lorem ipsum fr',
      'name_ar'    => 'lorem ipsum ar',
      'options_fr' =>  ActivitySector::cases(),
      'options_ar' =>  ActivitySector::cases(),
    ]);
  }
}
