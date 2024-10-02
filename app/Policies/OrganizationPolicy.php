<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Event;
use App\Models\Organization;

class OrganizationPolicy
{
  /**
   * Determine whether the user can view any models.
   */
  public function viewAny(User $user): bool
  {    
    return $user->is_admin ||  true;
  }

  /**
   * Determine whether the user can view the model.
   */
  public function view(User $user, Organization $organization): bool
  {
    return $user->is_admin || $user->id == $organization->user_id;
  }

  /**
   * Determine whether the user can create models.
   */
  public function create(User $user): bool
  {
    return $user->is_admin || true;
  }

  /**
   * Determine whether the user can update the model.
   */
  public function update(User $user, Organization $organization): bool
  {

    return $user->is_admin || $user->id == $organization->user_id;
  }

  /**
   * Determine whether the user can delete the model.
   */
  public function delete(User $user, Organization $organization): bool
  {
    return $user->is_admin || $user->id == $organization->user_id;
  }

  /**
   * Determine whether the user can restore the model.
   */
  public function restore(User $user, Organization $organization): bool
  {
    return $user->is_admin ||  false;
  }

  /**
   * Determine whether the user can permanently delete the model.
   */
  public function forceDelete(User $user, Organization $organization): bool
  {
    return $user->is_admin || true;
  }


  /**
   * Determine whether the user can detach a tag from a podcast.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Organization  $organization
   * @param  \App\Models\Event  $event
   * @return bool
   */

  public function attachAnyEvent(User $user , Organization $organization):bool
  {
    return false;
  }

}
