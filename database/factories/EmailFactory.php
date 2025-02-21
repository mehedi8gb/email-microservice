<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
class EmailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::inRandomOrder()->first()->id,
            'subject' => "test",
            'message' => $this->faker->text,
            'other_data' => $this->faker->randomElement([null, ['key' => 'value']]),
        ];
    }
}
