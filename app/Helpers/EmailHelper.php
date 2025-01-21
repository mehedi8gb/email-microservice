<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use App\Models\SmtpConfig;
use Exception;

class EmailHelper
{
    /**
     * Configure mail settings dynamically for a company
     * @throws Exception
     */
    public static function configureMailSettings($companyId): void
    {
        $smtp = SmtpConfig::where('company_id', $companyId)->first();

        if (!$smtp) {
            throw new Exception("SMTP settings not found for company ID: {$companyId}");
        }

        // Set mail configurations dynamically
        Config::set('mail.mailers.smtp', [
            'transport' => 'smtp',
            'host' => $smtp->host,
            'port' => $smtp->port,
            'encryption' => $smtp->encryption,
            'username' => $smtp->username,
            'password' => $smtp->password,
            'timeout' => null,
            'auth_mode' => null,
        ]);

        Config::set('mail.default', 'smtp');
    }

    /**
     * Send an email dynamically
     * @throws Exception
     */
    public static function sendEmail($companyId, $from, $to, $subject, $message): string
    {
        self::configureMailSettings($companyId);

        Mail::raw($message, function ($mail) use ($from, $to, $subject) {
            $mail->to($to)
                ->subject($subject)
                ->from($from, 'Company Name');
        });

        return "Email sent successfully to {$to}";
    }
}
