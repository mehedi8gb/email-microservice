<?php

namespace App\Jobs;

use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Exception;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $companyId;
    protected $smtpConfig;
    protected $toEmail;
    protected $message;
    protected $subject;

    /**
     * Create a new job instance.
     */
    public function __construct($companyId, $smtpConfig, $toEmail, $subject, $message)
    {
        $this->companyId = $companyId;
        $this->smtpConfig = $smtpConfig;
        $this->toEmail = $toEmail;
        $this->message = $message;
        $this->subject = $subject;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Dynamically set mail config using the SMTP configuration
            Config::set('mail.mailers.smtp', [
                'transport'  => 'smtp',
                'host'       => $this->smtpConfig->host,
                'port'       => $this->smtpConfig->port,
                'encryption' => $this->smtpConfig->encryption,
                'username'   => $this->smtpConfig->username,
                'password'   => $this->smtpConfig->password,
            ]);
            Config::set('mail.default', 'smtp');

            // Send the email
            Mail::raw($this->message, function ($mail) {
                $mail->to($this->toEmail)
                    ->subject($this->subject)
                    ->from($this->smtpConfig->from_email);  // or use a 'from' email from the SMTP config
            });

            // Log the email in the database
            EmailLog::create([
                'company_id' => $this->companyId,
                'from_email' => $this->smtpConfig->from_email,  // Assuming the 'username' as the sender
                'to_email'   => $this->toEmail,
                'message'    => $this->message,
                'status'     => 'sent',
            ]);
        } catch (Exception $e) {
            // Log failure in database
            EmailLog::create([
                'company_id' => $this->companyId,
                'from_email' => $this->smtpConfig->from_email,
                'to_email'   => $this->toEmail,
                'message'    => $this->message,
                'status'     => 'failed',
                'error'      => $e->getMessage()
            ]);
        }
    }
}


