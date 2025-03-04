<?php

namespace App\Jobs;

use App\Models\EmailLog;
use App\Models\EmailNotification;
use App\Models\EmailTemplate;
use App\Notifications\EmailNotificationMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Support\Facades\Notification;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $companyId;
    protected $smtpConfig;
    protected string $toEmail;
    protected string $message;
    protected string $subject;
    protected $log;

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
    }

    public function handle(): void
    {
        try {
            // Configure SMTP settings dynamically
            Config::set('mail.mailers.smtp', [
                'transport' => 'smtp',
                'host' => $this->smtpConfig->host,
                'port' => $this->smtpConfig->port,
                'encryption' => $this->smtpConfig->encryption,
                'username' => $this->smtpConfig->username,
                'password' => $this->smtpConfig->password,
            ]);
            Config::set('mail.default', 'smtp');

            Mail::send([], [], function ($mail) {
                // Determine if the message contains HTML tags
                $isHtml = $this->isHtml($this->message);

                $mail->to($this->toEmail)
                    ->subject($this->subject)
                    ->from($this->smtpConfig->from_email);

                if ($isHtml) {
                    // If the message is HTML, set the body as HTML
                    $mail->html($this->message);
                } else {
                    // If the message is plain text, set the body as text
                    $mail->text($this->message);
                }
            });




            // Update email log
            $this->log->update(['status' => 'sent']);

            // Notify all subscribers
            $subscribers = EmailNotification::where('company_id', $this->companyId)->pluck('email')->toArray();

            Notification::route('mail', $subscribers)->notify(new EmailNotificationMessage($this->toEmail, $this->subject));

        } catch (Exception $e) {
            $this->log->update([
                'status' => 'failed',
                'error' => $e->getMessage()
            ]);
        }
    }
    private function isHtml(string $message): bool
    {
        // Check if the message contains HTML tags
        return (bool) preg_match('/<[^>]+>/', $message);
    }
}


