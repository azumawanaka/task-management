<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Carbon\Carbon;

class DeletOldTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete soft-deleted tasks older than 30 days';

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
     */
    public function handle()
    {
        $date = Carbon::now()->subDays(30);
        $deletedTasks = Task::onlyTrashed()->where('deleted_at', '<', $date)->forceDelete();

        $this->info("Deleted $deletedTasks old soft-deleted tasks.");

        return 0;
    }
}
