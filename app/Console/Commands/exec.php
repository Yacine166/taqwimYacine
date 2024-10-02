<?php

namespace App\Console\Commands;

use App\Models\Parameter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class exec extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:exec';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        function toArray($string)
        {
            return explode(',',str_replace(['[',']'],'',$string));
        }

        function oneQuote($string){
            return str_replace( "'",'', $string);
        }

        $parameters = Parameter::all();

        DB::beginTransaction();
        foreach ($parameters as $parameter) {
            $parameter->update([
                'options'=> oneQuote($parameter->options),
                'options_fr'=> oneQuote($parameter->options_fr),
                'options_ar'=> oneQuote($parameter->options_ar),
            ]);
        }

        DB::commit();
    }
}
