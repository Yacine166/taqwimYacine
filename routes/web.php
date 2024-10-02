<?php

use App\Models\User;
use App\Jobs\NotifySingleUser;
use App\Models\EventOrganization;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
  return response()->json("gg");
});

Route::get('/test', function () {
    $user = User::find(20);
    $organization = $user->organizations->first();
    $eventInstance = EventOrganization::where('organization_id', $organization->id)->first();
    $job = NotifySingleUser::dispatch($eventInstance);

    Artisan::call('queue:monitor database:default');
    var_dump(Artisan::output());
})->middleware('isAdmin');

require __DIR__ . '/auth.php';
