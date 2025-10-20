<?php

namespace App\Console\Commands;

use App\Services\LeaveBalanceService;
use Illuminate\Console\Command;

class ResetLeaveBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaves:reset-balances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset leave balances for new year and carry forward Annual and Compensation leaves';

    protected LeaveBalanceService $leaveBalanceService;

    public function __construct(LeaveBalanceService $leaveBalanceService)
    {
        parent::__construct();
        $this->leaveBalanceService = $leaveBalanceService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Resetting leave balances for new year...');
        
        try {
            $this->leaveBalanceService->resetAllBalancesForNewYear();
            
            $this->info('Leave balances reset successfully!');
            $this->info('Annual and Compensation leave balances have been carried forward until March 31.');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to reset leave balances: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
