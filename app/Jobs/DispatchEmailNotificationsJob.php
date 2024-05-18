<?php

namespace App\Jobs;

use App\Mail\PaymentCheckoutConfirmationMail;
use App\Mail\PaymentCheckoutReceiptMail;
use App\Mail\SubscriptionRenewalConfirmationMail;
use App\Mail\UserForgotPasswordMail;
use App\Mail\UserVerificationMail;
use App\Utils\Enums\EmailTypes;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DispatchEmailNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $jobPayload;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct($jobPayload)
    {
        $this->jobPayload = $jobPayload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $emailData = $this->jobPayload;
        try {
            Log::info("Email type " . $emailData['type']);
            Log::info($emailData);
            $email = null;
            switch ($emailData['type']) {
                case EmailTypes::USER_VERIFICATION->name:
                    $email = new UserVerificationMail($emailData);
                    break;
                case EmailTypes::USER_FORGOT_PASSWORD->name:
                    $email = new UserForgotPasswordMail($emailData);
                    break;
            }
            Log::info("sent email to " . $emailData['recipientEmail']);
            Mail::to($emailData['recipientEmail'])->send($email);
        } catch (Exception $e) {
            Log::error('Error sending email: ' . $e->getMessage());
        }
    }
}
