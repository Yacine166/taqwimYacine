<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Csv extends Command
{

    protected $signature = 'app:csv {file_path} {model_name}';

    protected $description = 'Seed database throught csv file';

    public function handle()
    {
        $file_path = $this->argument('file_path');
        $model_name = Str::title($this->argument('model_name'));

        $data = Helper::csvToArray($file_path);
        $class= "App\\Models\\" . $model_name;
        DB::beginTransaction();
        foreach ($data as $record) {
            $class::create($record);
        }
        DB::commit();

        $this->info(count($data) . ' '. $model_name . ' records added');
    }

}
