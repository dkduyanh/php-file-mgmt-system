<?php

namespace app\library\events;

use Enqueue\AmqpLib\AmqpConnectionFactory;
use Interop\Amqp\AmqpQueue;
use Interop\Amqp\AmqpTopic;
use Interop\Amqp\Impl\AmqpBind;

class InvalidateCache
{
    private $_exchangeName = 'danet.cache';
    private $_queueName = 'danet.cache.invalidated';

    private $_queue = null;
    private $_topic = null;

    private static $_instance = null;
    protected $_amqpChannel = null;

    public static function getInstance()
    {
        if(self::$_instance === null){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct(array $config = [])
    {
        $factory = new AmqpConnectionFactory([
            'host' => AMQP_HOST,
            'port' => AMQP_PORT,
            'vhost' => '/',
            'user' => AMQP_USER,
            'pass' => AMQP_PASSWORD,
            'persisted' => false,
        ]);
        $this->_amqpChannel = $context = $factory->createContext();

        //Declare topic (exchange)
        $this->_topic = $context->createTopic($this->_exchangeName);
        $this->_topic->setType(AmqpTopic::TYPE_TOPIC);
        $this->_topic->setFlags(AmqpTopic::FLAG_DURABLE);
        $context->declareTopic($this->_topic);

        //Declare queue.
        $this->_queue = $context->createQueue($this->_queueName);
        $this->_queue->addFlag(AmqpQueue::FLAG_DURABLE);
        $context->declareQueue($this->_queue);

        //Bind queue to topic
        $context->bind(new AmqpBind($this->_topic, $this->_queue));
    }

    protected function publish($message)
    {
        $message = $this->_amqpChannel->createMessage($message);
        $this->_amqpChannel->createProducer()->send($this->_queue, $message);
    }


    public function invalidateChannel($channel)
    {
        if(SYNC_DATA_CHANGES) {
            $this->publish(json_encode([
                'type' => 'channel',
                'value' => [
                    'id' => $channel->id,
                    'name' => $channel->name,
                ]
            ]));
        }
    }

    public function invalidatePlatform($platform)
    {
        if(SYNC_DATA_CHANGES) {
            $this->publish(json_encode([
                'type' => 'platform',
                'value' => [
                    'id' => $platform->id,
                    'code' => $platform->code,
                ]
            ]));
        }
    }

    public function invalidateApiKey($apiKey)
    {
        if(SYNC_DATA_CHANGES) {
            $this->publish(json_encode([
                'type' => 'api_key',
                'value' => [
                    'id' => $apiKey->id,
                    'key' => $apiKey->key,
                ]
            ]));
        }
    }

    public function invalidateMovie($movie)
    {
        if(SYNC_DATA_CHANGES){
            $this->publish(json_encode([
                'type' => 'movie',
                'value' => [
                    'id' => $movie->id,
                    'uid' => $movie->timecode
                ]
            ]));

            //Update movies in playlist
            foreach($movie->playlists as $playlist){
                $this->invalidatePlaylist($playlist);
            }

            //update movies packages
            foreach($movie->packages as $package){
                $this->invalidatePackage($package);
            }

            //update movies in caoursel


            //Yii::$app->redis->set('DANET_T_TAG_MOVIE_UID_'.$movie->timecode, self::getMircrotime());
            //return Yii::$app->redis->set('DANET_T_TAG_MOVIE_ID_'.$movie->id, self::getMircrotime());
        }
    }

    public function invalidateEpisode($episode)
    {
        if(SYNC_DATA_CHANGES) {
            $this->publish(json_encode([
                'type' => 'episode',
                'value' => [
                    'id' => $episode->id,
                    'uid' => $episode->timecode,
                    'movie_id' => $episode->movie_id,
                ]
            ]));
        }
    }

    public function invalidateEpisodeLocation($location)
    {
        if(SYNC_DATA_CHANGES) {
            $this->publish(json_encode([
                'type' => 'episode_location',
                'value' => [
                    'movie_id' => $location->episode->movie_id,
                    'episode_id' => $location->episode_id,
                ]
            ]));
        }
    }

    public function invalidatePlaylist($playlist)
    {
        if(SYNC_DATA_CHANGES) {
            $this->publish(json_encode([
                'type' => 'playlist',
                'value' => [
                    'id' => $playlist->id,
                    'package_id' => $playlist->package_id,
                ]
            ]));

            //Yii::$app->redis->set('DANET_T_TAG_PLAYLIST_PACKAGE_' . $playlist->package_id, self::getMircrotime());
            //return Yii::$app->redis->set('DANET_T_TAG_PLAYLIST_ID_' . $playlist->id, self::getMircrotime());
        }
    }

    public function invalidateCarousels($caoursel)
    {
        if (SYNC_DATA_CHANGES) {
            $this->publish(json_encode([
                'type' => 'carousel',
                'value' => [
                    'id' => $caoursel->id,
                    'package_id' => $caoursel->package_id,
                ]
            ]));
            //return Yii::$app->redis->set('DANET_T_TAG_CAROUSEL_PACKAGE_' . $caoursel->package_id, self::getMircrotime());
        }
    }

    public function invalidatePackage($package)
    {
        if (SYNC_DATA_CHANGES) {
            $this->publish(json_encode([
                'type' => 'package',
                'value' => [
                    'id' => $package->id,
                ]
            ]));
        }
    }

    public function invalidateSubscription($subscription)
    {
        if (SYNC_DATA_CHANGES) {
            $this->publish(json_encode([
                'type' => 'subscription',
                'value' => [
                    'id' => $subscription->id,
                    'package_id' => $subscription->package_id,
                    'channel_id' => $subscription->package->channel->id,
                ]
            ]));
        }
    }

    public function invalidateRelease($release)
    {
        if (SYNC_DATA_CHANGES) {
            $this->publish(json_encode([
                'type' => 'release',
                'value' => [
                    'id' => $release->id,
                    'channel_id' => $release->channel_id,
                    'platform' => $release->platform,
                    'version' => $release->version,
                ]
            ]));
        }
    }

    public function invalidateCustomer($customer)
    {
        if (SYNC_DATA_CHANGES) {
            $this->publish(json_encode([
                'type' => 'customer',
                'value' => [
                    'id' => $customer->id,
                    'uid' => $customer->user_id,
                ]
            ]));
        }
    }

    protected static function getMircrotime()
    {
        return round(microtime(true)*1000);
    }
}
