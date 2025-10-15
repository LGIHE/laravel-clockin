<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUserAccountMail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify email configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Attempting to send test email to: {$email}");
        $this->info("MAIL_HOST: " . config('mail.mailers.smtp.host'));
        $this->info("MAIL_PORT: " . config('mail.mailers.smtp.port'));
        $this->info("MAIL_USERNAME: " . config('mail.mailers.smtp.username'));
        $this->info("MAIL_ENCRYPTION: " . config('mail.mailers.smtp.encryption'));
        $this->info("MAIL_FROM_ADDRESS: " . config('mail.from.address'));
        
        try {
            // Generate a test setup URL
            $testSetupUrl = url('/account-setup/test-token-' . time());
            
            Mail::to($email)->send(
                new NewUserAccountMail(
                    'Test User',
                    $email,
                    $testSetupUrl
                )
            );
            
            $this->info("✓ Email sent successfully!");
            $this->info("Please check the inbox for: {$email}");
            $this->info("Also check spam/junk folder if not found in inbox.");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("✗ Failed to send email!");
            $this->error("Error: " . $e->getMessage());
            $this->error("\nFull trace:");
            $this->error($e->getTraceAsString());
            
            return 1;
        }
    }
}
