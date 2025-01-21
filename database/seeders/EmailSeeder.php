<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Email;
use App\Models\SMTPConfig;
use Illuminate\Database\Seeder;

class EmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::factory()->count(1)->create();
        Email::factory()->count(1)->create();
        SmtpConfig::factory()->count(1)->create();
    }
}
