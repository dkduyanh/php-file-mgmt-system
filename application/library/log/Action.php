<?php
/**
 * This class is used to collect and store user action logs to Database
 * @author: dkduyanh17@gmail.com
 */

namespace app\library\log;

use yii\base\Component;
use yii\db\Connection;
use yii\di\Instance;
use yii\helpers\VarDumper;

class Action extends Component
{
    public $db = 'db';
    public $logTable = '{{%user_action_log}}';
    protected $messages = [];

    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
        register_shutdown_function(function () {
            // make regular flush before other shutdown functions, which allows session data collection and so on
            $this->flush();
            // make sure log entries written by shutdown functions are also flushed
            // ensure "flush()" is called last when there are multiple shutdown functions
            register_shutdown_function([$this, 'flush'], true);
        });
    }

    public function log($message, $level, $category)
    {
        $time = time();
        $this->messages[] = [$message, $level, $category, $time];
        $this->export();
    }

    protected function collect($messages, $final)
    {
        $this->messages = array_merge($this->messages, static::filterMessages($messages, $this->getLevels(), $this->categories, $this->except));
        $count = count($this->messages);
        if ($count > 0 && ($final || $this->exportInterval > 0 && $count >= $this->exportInterval)) {
            if (($context = $this->getContextMessage()) !== '') {
                $this->messages[] = [$context, Logger::LEVEL_INFO, 'application', YII_BEGIN_TIME, [], 0];
            }
            // set exportInterval to 0 to avoid triggering export again while exporting
            $oldExportInterval = $this->exportInterval;
            $this->exportInterval = 0;
            $this->export();
            $this->exportInterval = $oldExportInterval;

            $this->messages = [];
        }
    }

    protected function export()
    {
//        if ($this->db->getTransaction()) {
//            $this->db = clone $this->db;
//        }

        $tableName = $this->db->quoteTableName($this->logTable);
        $sql = "INSERT INTO $tableName ([[level]], [[action]], [[message]], [[created_at]])
                VALUES (:level, :action, :message, :created_at)";
        $command = $this->db->createCommand($sql);
        foreach ($this->messages as $message) {
            list($text, $level, $category, $timestamp) = $message;
            if (!is_string($text)) {
                // exceptions may not be serializable if in the call stack somewhere is a Closure
                if ($text instanceof \Throwable || $text instanceof \Exception) {
                    $text = (string) $text;
                } else {
                    $text = VarDumper::export($text);
                }
            }
            if ($command->bindValues([
                    ':level' => $level,
                    ':action' => $category,
                    ':message' => $text,
                    ':created_at' => $timestamp,
                ])->execute() > 0) {
                continue;
            }
            throw new LogRuntimeException('Unable to export log through database!');
        }
    }
}