<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BetaUserController;
use App\Http\Controllers\MailchimpController;
use App\Http\Controllers\Api\ParameterController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\AuthenticatedUserController;
use App\Http\Controllers\Api\OrganizationEventController;
use App\Http\Controllers\Api\OrganizationCategoryController;

Route::group(['prefix' => 'me', 'middleware' => ['auth:sanctum','locale']], function () {

  Route::controller(AuthenticatedUserController::class)->group(function () {
    Route::get('/', 'show');

    Route::post('/', 'destroy');

    Route::put('/', 'update');

    Route::put('/password',  'updatePassword');

    Route::put('/language',  'updateLanguage');
  });

  Route::get('parameters', ParameterController::class)->name('parameter.index');

  Route::apiResource('/organizations', OrganizationController::class, ['names' => 'organization']);

  Route::apiResource('/organizations.events', OrganizationEventController::class, ['names' => 'organization.event'])->only(['index', 'update','show']);

  Route::get('/organizations/{organization}/count', OrganizationEventController::class . "@count");

  //category notification settings
  Route::apiResource('/organizations.categories', OrganizationCategoryController::class, ['names' => 'organization.category'])->only(['index', 'update']);
});


Route::post('/beta', BetaUserController::class);

Route::post('/newsLetter', MailchimpController::class);

Route::get('/newsLetter', MailchimpController::class . "@ping");
