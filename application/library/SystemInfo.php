<?php
namespace app\library;

use Yii;

/**
 * Class Ping
 * @package app\library
 * @author dkduyanh17@gmail.com
 */
class SystemInfo
{


    public static function pingMysql()
    {
        $mysqli = new \mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        return $mysqli->ping();
    }

    public static function pingRedis()
    {
        try{
            return Yii::$app->redis->executeCommand('ping');
        } catch (\Exception $e){
            return false;
        }
    }

    public static function redisInfo($section = false)
    {
        try{
            if($section){
                return Yii::$app->redis->executeCommand("info {$section}");
            }
            return Yii::$app->redis->executeCommand("info");
        } catch (\Exception $e){
            return null;
        }
    }

    public static function pingSolr()
    {
        // execute the ping query
        try {
            // create a ping query
            $ping = Yii::$app->solr->createPing();

            $result = Yii::$app->solr->ping($ping);

            if(isset($result->getData()['status']) && $result->getData()['status'] == 'OK'){
                return true;
            }

        } catch (Solarium\Exception $e) {
            return false;
        }
    }

    public static function solrInfo($section = false)
    {
        // execute the ping query
        try {
            // create a ping query
            $client = Yii::$app->solr->solr;

            // create a core admin query
            $coreAdminQuery = $client->createCoreAdmin();

            // use the core admin query to build a Status action
            $statusAction = $coreAdminQuery->createStatus();
            $coreAdminQuery->setAction($statusAction);
            $response = $client->coreAdmin($coreAdminQuery);
            return $statusResults = $response->getStatusResults();

            $msg = '';
            foreach($statusResults as $statusResult) {
                $msg .= $statusResult->getCoreName() . ': ' . $statusResult->getUptime();
            }
            return $msg;
        } catch (Solarium\Exception $e) {
            return false;
        }
    }
}