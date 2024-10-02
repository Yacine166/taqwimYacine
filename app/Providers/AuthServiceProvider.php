<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Policies\EventPolicy;
use App\Models\EventOrganization;
use App\Policies\OrganizationPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
  /**
   * The policy mappings for the application.
   *
   * @var array<class-string, class-string>
   */
  protected $policies = [
    Organization::class => OrganizationPolicy::class,
    Event::class => EventPolicy::class,
  ];
  /**
   * Register any authentication / authorization services.
   */
  public function boot(): void
  {
    $this->registerPolicies();

    ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
      return env("LANDING_FRONTEND_URL", "https://taqwim.app")."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
    });

    Gate::define('view-organization', [OrganizationPolicy::class, 'view']);
    Gate::define('update-organization', [OrganizationPolicy::class, 'update']);

    Gate::define('update-organizationEvent', function (User $user,Organization $organization,EventOrganization $event ) {
      return $user->id == $organization->user_id && $event->organization_id == $organization->id;
    });
  }
}
