<?php


namespace app\library\events;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


class ApiDataRefresher
{
    private static $_instance = null;
    protected $_amqpChannel = null;

    private function __construct(array $config = [])
    {
        $amqpConnection = AMQPStreamConnection::create_connection([
            ['host' => AMQP_HOST, 'port' => AMQP_PORT, 'user' => AMQP_USER, 'password' => AMQP_PASSWORD, 'vhost' => '/'],
        ], $config);

        $exchange = 'danet.api';
        $exchangeType = 'fanout';
        $routingKeys = ['system.evt.refresh_localcache'];

        $this->_amqpChannel = $amqpConnection->channel();
        $this->_amqpChannel->exchange_declare($exchange, $exchangeType, false, true, false);
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
        return $this->_amqpChannel->basic_publish($msg, 'danet.api', 'system.evt.refresh_localcache');
    }


    public function invalidateChannel($channel)
    {
        if(SYNC_DATA_CHANGES) {
            $this->publish(json_encode([
                'type' => 'channel',
            ]));
        }
    }

    public function invalidatePlatform($platform)
    {
        if(SYNC_DATA_CHANGES) {
            $this->publish(json_encode([
                'type' => 'platform',
            ]));
        }
    }

    public function invalidateApiKey($apiKey)
    {
        if(SYNC_DATA_CHANGES) {
            $this->publish(json_encode([
                'type' => 'api_key',
            ]));
        }
    }
}