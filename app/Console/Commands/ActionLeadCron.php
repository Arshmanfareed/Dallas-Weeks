<?php

namespace App\Console\Commands;

use App\Http\Controllers\ActionsController;
use Illuminate\Console\Command;

class ActionLeadCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'action:lead';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for lead actions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $actionController = new ActionsController();
            $actionController->update_leads_action();
            $this->info('Data inserted successfully.'.now());
        } catch (\Exception $e) {
            $this->error('Failed to insert data: ' . $e->getMessage());
        }
    }
}
