<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Helpers\Helper;
use App\Services\OneSignal;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Models\EventOrganization;
use Illuminate\Support\Facades\App;


class Notify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'testing one signal notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{

            $eventInstance=EventOrganization::latest()->first();

            $locale = 'fr'; //test locales ar , fr , en

            App::setLocale($locale);

            $oneSignal = new OneSignal();

            $user_external_id = 'external_id_' . '20'; //zack's id , replace to test yours

            $heading = __('notification.heading') .' '. $eventInstance->deadline->translatedFormat('d F Y');

            $content = Helper::notificationContentTranslationHelper($locale,$eventInstance);

            $icon = "https://taqwim.aivot.monaam.com" . "/storage/" . "qXrQVfSB9uIPXaJZfoDDhrIXaqgDdi5NmqLvElyQ.png" ;

            $notification = $oneSignal->createNotification($heading,$content,$icon,$user_external_id);

            $result = $oneSignal->getApi()->createNotification($notification);

            dd($result);

        } catch(\Throwable $e){
            throw $e;
        }

    }

}
