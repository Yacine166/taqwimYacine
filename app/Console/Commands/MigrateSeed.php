<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

class MigrateSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'runs db:seed --seed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
      Artisan::call("db:seed --force");
    }
}
