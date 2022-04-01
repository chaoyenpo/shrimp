<?php

namespace App\Models\System\Services;

use sngrl\PhpFirebaseCloudMessaging\Client;
use sngrl\PhpFirebaseCloudMessaging\Message;
use sngrl\PhpFirebaseCloudMessaging\Recipient\Device;
use sngrl\PhpFirebaseCloudMessaging\Notification;

class FCMService
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function send2Devices($tokens, $title, $body, $pairs = [])
    {
        $server_key = env('FCM_SECRET_KEY');
        $this->client->setApiKey($server_key);
        $this->client->injectGuzzleHttpClient(new \GuzzleHttp\Client());

        $message = new Message();
        $message->setPriority('high');
        foreach($tokens as $token){
            $message->addRecipient(new Device($token));
        }
        $message->setNotification(new Notification($title, $body));
        if (!empty($pairs)) $message->setData($pairs);

        return $this->client->send($message);
    }
}
