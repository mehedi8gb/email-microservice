<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => "JumpIntoJob",
            'email' => "info@jumpintojob.com"
//            'logo' => $this->faker->imageUrl(),
//            'website' => $this->faker->url,
//            'created_at' => now(),
//            'updated_at' => now(),
        ];
    }
}
