<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'=>strtoupper(fake()->word()),
            'details'=>fake()->paragraph(5),
            'category_id'=>fake()->randomElement(Category::pluck('id')),
            'cycle_month'=>fake()->randomElement([ "monthly",...range(1, 12, 1)]),
            'cycle_day'=>fake()->randomElement([fake()->date('d'),now()->day])
        ];
    }
}
