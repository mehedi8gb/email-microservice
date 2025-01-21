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
        SMTPConfig::factory()->create([
            'company_id' => Company::first()->id,
            'host' => "mail.jumpintojob.com",
            'port' => 465,
            'username' => "dev-info@jumpintojob.com",
            'password' => "i;p93:fNnIMT14",
            'encryption' => "tls",
            'from_email' => "dev-info@jumpintojob.com"
        ]);
    }
}
