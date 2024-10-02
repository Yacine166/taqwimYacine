<?php

namespace App\Jobs;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewYearAttachEvents implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * Create a new job instance.
   */
  public $orgs;
  public function __construct()
  {
    $this->orgs = Organization::all();

  }
  /**
   * Execute the job.
   */
  public function handle(): void
  {
    foreach ($this->orgs as $org) {

        try {
            $org->attachEvents();
        }catch(\Throwable $e){
            continue;
        }
}
  }
}
