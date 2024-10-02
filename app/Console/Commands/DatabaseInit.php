<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Enums\Person;
use App\Models\Event;
use App\Enums\Cacobatph;
use App\Models\Category;
use App\Enums\TaxAbility;
use App\Models\Parameter;
use App\Enums\EmployeesNumber;
use Illuminate\Console\Command;

class DatabaseInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:database-init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
    User::create([
      'name'              => 'brainiac',
      'email'             => env('ADMIN_EMAIL'),
      'password'          => bcrypt(env('ADMIN_PASSWORD')),
      'phone'             => env('ADMIN_PHONE', '0123456789'),
      'email_verified_at' => now(),
      'is_admin'          => true
    ]);
    $categories = [
      ['name' => 'IRG', 'color' => '#FF0000'],
      ['name' => 'CNAS', 'color' => '#00FF00'],
      ['name' => 'CASNOS', 'color' => '#FFFF00'],
      ['name' => 'CACOBATPH', 'color' => '#00FFFF']
    ];

    foreach ($categories as $cat) {
      Category::create($cat);
    }
    $param1 = Parameter::create([
      'name'       => 'number of employees',
      'slug'       => 'employees_number',
      'options'    => EmployeesNumber::cases()
    ]);

    $param2 = Parameter::create([
      'name'       => 'tax ability',
      'slug'       => 'tax_ability',
      'options'    => TaxAbility::cases()
    ]);

    $param3 = Parameter::create([
      'name'       => 'cacobatph',
      'slug'       => 'cacobatph',
      'options'    => Cacobatph::cases()
    ]);

    $param3 = Parameter::create([
      'name'       => 'person',
      'slug'       => 'person',
      'options'    => Person::cases()
    ]);
    $events = [
      //IRG
      ['name' => 'DECLARATION MENSUELLE', 'category_id' => 1,  'cycle_day' => 20],
      ['name' => 'DECLARATION GLOBALE DES REVENUS', 'category_id' => 1,  'cycle' => 12, 'cycle_month' => '4', 'cycle_day' => 30],
      ['name' => "DECLARATION DES BENEFICES DES SOCIETES", 'category_id' => 1,  'cycle' => 12, 'cycle_month' => '4', 'cycle_day' => 30],
      ['name' => "DECLARATION DE LA TAXE SUR L'ACTIVITE PROFESSIONNELLE.", 'category_id' => 1,  'cycle' => 12, 'cycle_month' => '4', 'cycle_day' => 30],
      ['name' => "Déclaration annuelle rectificative de l'IBS", 'category_id' => 1,  'cycle' => 12, 'cycle_month' => '5', 'cycle_day' => 20],
      ['name' => "La taxe de la formation professionnelle continue et d'apprentissage ", 'category_id' => 1,  'cycle' => 12, 'cycle_month' => '2', 'cycle_day' => 20],

      //CNAS
      [
        'name' => "Déclaration d'assiette de cotisation", 'category_id' => 2,
        "cycle" => '3',
        'cycle_day' => 20
      ],
      [
        'name' => "Déclaration d'assiette de cotisation", 'category_id' => 2,
        'cycle_day' => 20
      ],
      [
        'name' => "Déclaration annuelle des salaires et des salariés", 'category_id' => 2,  'cycle' => 12, 'cycle_month' => '1', 'cycle_day' => 31
      ],

      //CASNOS
      ['name' => "Déclaration d'assiette de cotisation", 'category_id' => 3,  'cycle' => 12, 'cycle_month' => '1', 'cycle_day' => 31],
      ['name' => "Déclaration et paiement des cotisations ", 'category_id' => 3,  'cycle' => 12, 'cycle_month' => '6', 'cycle_day' => 30],

      //CACOBATH
      [
        'name' => "Déclaration d'assiette de cotisation", 'category_id' => '4',
        "cycle" => '3',
        'cycle_day' => 20
      ],
      [
        'name' => "Déclaration d'assiette de cotisation", 'category_id' => '4',
        'cycle_day' => 20
      ],
      [
        'name' => "Déclaration annuelle des salaires et des salariés", 'category_id' => '4',  'cycle' => 12, 'cycle_month' => '1', 'cycle_day' => 31
      ]
    ];

    foreach ($events as $event) {
      Event::create($event);
    }

    $events = Event::all();
    $event1 = $events->find(1)->parameters()->syncWithPivotValues(2, ['value' => 'Real', 'condition' => '='], false);
    $event2 = $events->find(2)->parameters()->syncWithPivotValues(4, ['value' => 'Physic',  'condition' => '='], false);
    $event3 = $events->find(3)->parameters()->syncWithPivotValues(4, ['value' => 'Moral',  'condition' => '='], false);
    $event4 = $events->find(4)->parameters()->syncWithPivotValues(2, ['value' => 'Real',  'condition' => '='], false);
    $event5 = $events->find(5)->parameters()->syncWithPivotValues(2, ['value' => 'Real',  'condition' => '='], false);
    $event6 = $events->find(6);
    $event7 = $events->find(7)->parameters()->syncWithPivotValues(1, ['value' => '10-50',  'condition' => '<'], false);
    $event8 = $events->find(8)->parameters()->syncWithPivotValues(1, ['value' => '0-9',  'condition' => '>'], false);
    $event9 = $events->find(9);
    $event10 = $events->find(10);
    $event11 = $events->find(11);
    $event12 = $events->find(12);
    $event12->parameters()->syncWithPivotValues(1, ['value' => '10-50',  'condition' => '<'], false);
    $event12->parameters()->syncWithPivotValues(3, ['value' => 'Affiliated',  'condition' => '='], false);
    $event13 = $events->find(13);
    $event13->parameters()->syncWithPivotValues(1, ['value' => '0-9',  'condition' => '>'], false);
    $event13->parameters()->syncWithPivotValues(3, ['value' => 'Affiliated',  'condition' => '='], false);
    $event14 = $events->find(14);
    }
}
