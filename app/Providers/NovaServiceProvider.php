<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    parent::boot();
    Nova::sortResourcesBy(function ($resource) {
      return $resource::$priority ?? 9999;
    });
  }

  /**
   * Register the Nova routes.
   *
   * @return void
   */
  protected function routes()
  {
    Nova::routes()
      ->withAuthenticationRoutes()
      ->withPasswordResetRoutes()
      ->register();
  }

  /**
   * Register the Nova gate.
   *
   * This gate determines who can access Nova in non-local environments.
   *
   * @return void
   */
  protected function gate()
  {
    Gate::define('viewNova', function ($user) {
      return $user->is_admin == true;
    });
  }

  /**
   * Get the dashboards that should be listed in the Nova sidebar.
   *
   * @return array
   */
  protected function dashboards()
  {
    return [
      new \App\Nova\Dashboards\Main,
    ];
  }

  /**
   * Get the tools that should be listed in the Nova sidebar.
   *
   * @return array
   */
  public function tools()
  {
    return [];
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  protected function resources()
  {
    Nova::resources([
      \App\Nova\User::class,
      \App\Nova\Organization::class,
      \App\Nova\Parameter::class,
      \App\Nova\Event::class,
      \App\Nova\Category::class,
      \App\Nova\Exception::class,
      \App\Nova\BetaUser::class,
    ]);
  }
}
