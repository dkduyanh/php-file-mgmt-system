<?php

namespace app\library\events;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ImageService
{

    private static $_instance = null;
    protected $_amqpChannel = null;

    private function __construct(array $config = [])
    {
        $amqpConnection = AMQPStreamConnection::create_connection([
            ['host' => AMQP_HOST, 'port' => AMQP_PORT, 'user' => AMQP_USER, 'password' => AMQP_PASSWORD, 'vhost' => '/'],
        ], $config);

        $exchange = 'danet.image';
        $exchangeType = 'direct';
        $queue = 'danet.image.deleted';
        $routingKeys = ['image.evt.delete'];

        $this->_amqpChannel = $amqpConnection->channel();
        $this->_amqpChannel->exchange_declare($exchange, $exchangeType, false, true, false);
//        $this->_amqpChannel->queue_declare($queue, false, false, false, false);
//
//        foreach($routingKeys as $routingKey){
//            $this->_amqpChannel->queue_bind($queue, $exchange, $routingKey);
//        }

    }

    public static function getInstance()
    {
        if(self::$_instance === null){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    protected function publish($message)
    {
        $properties = array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT);
        $msg = new AMQPMessage($message, $properties);
        return $this->_amqpChannel->basic_publish($msg, 'danet.image', 'image.evt.delete');
    }


    public function deleteImage($path)
    {
        if(SYNC_DATA_CHANGES) {
            //return Yii::$app->redis->set('DANET_T_TAG_CHANNEL_'.$channelName, self::getMircrotime());
            $this->publish(json_encode([
                'path' => $path,
            ]));
        }
    }
}
