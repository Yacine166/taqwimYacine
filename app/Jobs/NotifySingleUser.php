<?php

namespace App\Jobs;

use App\Helpers\Helper;
use App\Services\OneSignal;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NotifySingleUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable,SerializesModels;

    public function __construct(public $eventInstance){}

    public function handle(): void
    {
            $locale = $this->eventInstance->organization->user->locale ?? 'fr';

            App::setLocale($locale);

            $oneSignal = new OneSignal();

            $user_external_id = 'external_id_' . $this->eventInstance->organization->user->id;

            $heading = __('notification.heading') .' '. $this->eventInstance->deadline->translatedFormat('d F Y');

            $content = Helper::notificationContentTranslationHelper($locale,$this->eventInstance);

            $icon = config('app.url') . "/storage/". $this->eventInstance->event->category->image;

            $notification = $oneSignal->createNotification($heading,$content,$icon,$user_external_id);

            $oneSignal->getApi()->createNotification($notification);

    }
}
