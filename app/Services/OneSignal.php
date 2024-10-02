<?php
namespace App\Services;

use Illuminate\Console\Command;
use DateTime;
use onesignal\client\api\DefaultApi;
use onesignal\client\Configuration;
use onesignal\client\model\GetNotificationRequestBody;
use onesignal\client\model\Notification;
use onesignal\client\model\StringMap;
use onesignal\client\model\Player;
use onesignal\client\model\UpdatePlayerTagsRequestBody;
use onesignal\client\model\ExportPlayersRequestBody;
use onesignal\client\model\Segment;
use onesignal\client\model\FilterExpressions;
use PHPUnit\Framework\TestCase;
use GuzzleHttp;

class OneSignal {

    private $config;

    private $api;

    public function __construct() {
        $this->config = Configuration::getDefaultConfiguration()
            ->setAppKeyToken(env('ONESIGNAL_APP_KEY_TOKEN'))
            ->setUserKeyToken(env('ONESIGNAL_USER_KEY_TOKEN'));

        $this->api = new DefaultApi(
        new GuzzleHttp\Client(),
        $this->config
        );
    }

public function createNotification(string $enHeading,string $enContent,string $icon, array | string $external_ids): Notification {
    if (gettype($external_ids) == 'string')
        $external_ids = [$external_ids];

    $notification = new Notification();
    $notification->setAppId(env('ONESIGNAL_APP_ID'));

    $content = new StringMap();
    $content->setEn($enContent);

    $heading = new StringMap();
    $heading->setEn($enHeading);

    //building the notif ui
    $notification->setHeadings($heading);
    $notification->setContents($content);
    $notification->setSmallIcon($icon);
    $notification->setLargeIcon($icon);


    //todo it will be deprecated soon
    $notification->setIncludeExternalUserIds($external_ids);

    $notification->setChannelForExternalUserIds("push");

    return $notification;
}
public function getApi(){
    return $this->api;
}
}
