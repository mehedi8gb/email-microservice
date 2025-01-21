<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
class SmtpConfigFactory extends Factory
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
            'host' => "sandbox.smtp.mailtrap.io",
            'port' => 2525,
            'username' => "ac8f4e7efc1b4a",
            'password' => "097c9553ef02b9",
//            'password' => "i;p93:fNnIMT14",
            'encryption' => "tls",
            'from_email' => "info@jumpintojob.com",
            'from_name' => "Jump Into Job",
        ];
    }
}
