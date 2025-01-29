<?php

namespace Database\Seeders;

use App\Models\Company;
use Database\Factories\CompanyFactory;
use Database\Factories\EmailFactory;
use Database\Factories\SmtpConfigFactory;
use Illuminate\Database\Seeder;

class EmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanyFactory::times(1)->create();
        EmailFactory::times(1)->create();
        SmtpConfigFactory::times(1)->create();
        SMTPConfigFactory::times(1)->create([
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
