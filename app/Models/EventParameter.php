<?php

namespace App\Models;

use App\Models\Parameter;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventParameter extends Pivot
{
    use HasFactory;

    /**
     * Get the event associated with the EventParameter
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function event(): HasOne
    {
        return $this->hasOne(Event::class);
    }

    /**
     * Get the parameter associated with the ParameterParameter
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parameter(): HasOne
    {
        return $this->hasOne(Parameter::class);
    }
}