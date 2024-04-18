<?php

namespace App\Console\Commands;

use App\Http\Controllers\TestTable;
use Illuminate\Console\Command;

class TestCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
            $table = new TestTable();
            $table->insert_into_test_table();
            $this->info('Data inserted successfully.'.today());
        } catch (\Exception $e) {
            $this->error('Failed to insert data: ' . $e->getMessage());
        }
    }
}
