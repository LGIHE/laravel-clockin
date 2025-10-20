<?php

namespace Database\Seeders;

use App\Models\LeaveCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompensationLeaveCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if Compensation Leave category already exists
        $exists = LeaveCategory::where('name', 'LIKE', '%Compensation%')->exists();
        
        if (!$exists) {
            LeaveCategory::create([
                'id' => Str::uuid()->toString(),
                'name' => 'Compensation Leave',
                'description' => 'Leave accrued for working on weekends or public holidays',
                'max_in_year' => 0, // No fixed limit, accrued based on work
                'gender_restriction' => 'all',
            ]);
            
            $this->command->info('Compensation Leave category created successfully');
        } else {
            $this->command->info('Compensation Leave category already exists');
        }
    }
}
