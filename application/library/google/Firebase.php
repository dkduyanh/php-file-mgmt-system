<?php


namespace app\library\google;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use \Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\MessageTarget;
use Kreait\Firebase\ServiceAccount;
use yii\base\Model;

class Firebase extends Model
{
    protected $_firebase;
    protected $_message;

    public function init()
    {
        $serviceAccount = ServiceAccount::fromJsonFile(DATA_DIR.'/keys/danet-ef918-firebase-adminsdk-js10u-aa4ea7e19c.json');

        if($this->_firebase === null){
            $firebase = (new Factory)
                                    ->withServiceAccount($serviceAccount)
                                    ->withDatabaseUri('https://danet-ef918.firebaseio.com');

            if(YII_DEBUG || YII_ENV)
            {
                $firebase->withHttpClientConfig([
                    'debug' => true
                ]);
            }


            $this->_firebase = $firebase->create();
        }
        parent::init();
    }

    public function getDatabase()
    {
        return $this->_firebase->getDatabase();
    }

    public function getMessaging()
    {
        return $this->_firebase->getMessaging();
    }

    public function createMessage($title, $body, $imageUrl = null)
    {
        return CloudMessage::new() //::withTarget(MessageTarget::TOPIC, $topic)
            //->withNotification(Notification::create($title, $body, $imageUrl))
            ->withData([
                'title' => $title,
                'body' => $body,
                'image' => $imageUrl
            ]) // optional
        ;
    }

    public function send($message)
    {
        return $this->getMessaging()->send($message);
    }

    public function sendMulticast($message, array $deviceTokens)
    {
        return $this->getMessaging()->sendMulticast($message, $deviceTokens);
    }
}