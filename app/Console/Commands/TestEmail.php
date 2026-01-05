<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationSubmittedNotification;
use App\Models\ScholarshipApplication;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email functionality by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing email functionality...");
        $this->info("Sending test email to: {$email}");
        
        try {
            // Test simple email
            Mail::raw('This is a test email from the Scholarship Management System. If you receive this, email notifications are working correctly!', function($message) use ($email) {
                $message->to($email)
                       ->subject('Test Email - Scholarship Management System');
            });
            
            $this->info("✅ Simple test email sent successfully!");
            
            // Test with actual application notification if there's an application
            $application = ScholarshipApplication::first();
            if ($application) {
                $this->info("Testing application notification email...");
                Mail::to($email)->send(new ApplicationSubmittedNotification($application));
                $this->info("✅ Application notification email sent successfully!");
            } else {
                $this->warn("⚠️  No applications found to test application notification email");
            }
            
            $this->info("🎉 Email test completed! Check your inbox at: {$email}");
            $this->info("📧 Don't forget to check your spam/junk folder if you don't see the emails");
            
        } catch (\Exception $e) {
            $this->error("❌ Email test failed!");
            $this->error("Error: " . $e->getMessage());
            
            if (str_contains($e->getMessage(), 'authentication')) {
                $this->warn("🔑 This looks like an authentication issue. Please:");
                $this->warn("1. Enable 2-Factor Authentication on your Gmail account");
                $this->warn("2. Generate an App Password for Mail");
                $this->warn("3. Update MAIL_PASSWORD in your .env file with the App Password");
                $this->warn("4. Run: php artisan config:clear");
            }
        }
    }
}
