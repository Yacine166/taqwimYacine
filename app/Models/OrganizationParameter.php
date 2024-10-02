<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrganizationParameter extends Pivot
{
  /**
   * Get the organization associated with the OrganizationParameter
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function organization(): HasOne
  {
      return $this->hasOne(Organization::class,'id','parameter_id');
  }

  /**
   * Get the parameter associated with the ParameterParameter
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function parameter(): HasOne
  {
      return $this->hasOne(Parameter::class,'id','parameter_id');
  }
}
