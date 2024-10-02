<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Parameter extends Model
{
    use HasFactory;
  protected $guarded = [];
  protected $casts = [
    'options' => 'array',
    'options_fr' => 'array',
    'options_ar' => 'array',
  ];


    /**
     * Get all of the organizations for the Parameter
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)->withPivot('option')->withTimestamps();
    }

    /**
     * The events that belong to the Parameter
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)->withPivot(['value', 'condition', 'event_id'])->withTimestamps();
    }
}
