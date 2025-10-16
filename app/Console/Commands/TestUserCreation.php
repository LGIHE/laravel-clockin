<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;

class TestUserCreation extends Command
{
    protected $signature = 'user:test-create {email}';
    protected $description = 'Test user creation with email sending';

    public function handle(UserService $userService)
    {
        $email = $this->argument('email');
        
        $this->info("Creating test user with email: {$email}");
        $this->info("Mail configuration:");
        $this->info("- MAIL_MAILER: " . config('mail.default'));
        $this->info("- FROM_ADDRESS: " . config('mail.from.address'));
        
        // Get first available user level
        $userLevel = \DB::table('user_levels')->first();
        if (!$userLevel) {
            $this->error("No user levels found in database!");
            return 1;
        }
        
        try {
            $data = [
                'name' => 'Test User',
                'email' => $email,
                'password' => 'Test123!',
                'user_level_id' => $userLevel->id,
                'status' => 1,
            ];
            
            $this->info("");
            $this->info("Using user_level_id: {$userLevel->id} ({$userLevel->name})");
            $this->info("Calling UserService->createUser()...");
            
            $user = $userService->createUser($data);
            
            $this->info("✅ User created successfully!");
            $this->info("User ID: " . $user->id);
            $this->info("User Email: " . $user->email);
            $this->info("");
            $this->info("Check your Mailtrap inbox for the email!");
            $this->info("Also check storage/logs/laravel.log for email sending logs");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to create user!");
            $this->error("Error: " . $e->getMessage());
            $this->error("");
            $this->error("Full trace:");
            $this->error($e->getTraceAsString());
            
            return 1;
        }
    }
}
