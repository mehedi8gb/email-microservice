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

    protected int $companyId;
    protected $smtpConfig;
    protected string $toEmail;
    protected string $message;
    protected string $subject;
    protected EmailLog $log;

    /**
     * Create a new job instance.
     */
    public function __construct($companyId, $smtpConfig, $toEmail, $subject, $message, $log)
    {
        $this->companyId = $companyId;
        $this->smtpConfig = $smtpConfig;
        $this->toEmail = $toEmail;
        $this->message = $message;
        $this->subject = $subject;
        $this->log = $log;
        $this->log->refresh();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Dynamically set mail config using the SMTP configuration
            Config::set('mail.mailers.smtp', [
                'transport' => 'smtp',
                'host' => $this->smtpConfig->host,
                'port' => $this->smtpConfig->port,
                'encryption' => $this->smtpConfig->encryption,
                'username' => $this->smtpConfig->username,
                'password' => $this->smtpConfig->password,
            ]);
            Config::set('mail.default', 'smtp');

            // Send the email
            Mail::raw($this->message, function ($mail) {
                $mail->to($this->toEmail)
                    ->subject($this->subject)
                    ->from($this->smtpConfig->from_email);  // or use a 'from' email from the SMTP config
            });

            // Log the email in the database
            $this->log->update([
                'status' => 'sent',
            ]);
        } catch (Exception $e) {
            // Log failure in database
            $this->log->update([
                'status' => 'failed',
                'error' => $e->getMessage()
            ]);
        }
    }
}


