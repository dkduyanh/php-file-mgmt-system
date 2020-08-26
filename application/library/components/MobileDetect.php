<?php

namespace app\library\components;

use yii\base\Component;

/**
 * Class MobileDetect
 * @author: dkduyanh17@gmail.com
 * @package app\library\components
 */
class MobileDetect extends Component
{
    /**
     * @var \Mobile_Detect
     */
    protected $_mobileDetect;

    /**
     * @var self
     */
    static protected $_instance = null;

    public function init()
    {
        parent::init();
        $this->_mobileDetect = new \Mobile_Detect();
    }

    /**
     * @return self
     */
    static public function getInstance()
    {
        if (self::$_instance === null)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->_mobileDetect, $name], $arguments);
    }
    /**
     * @return bool
     */
    public function isDesktop()
    {
        if ($this->isMobile() || $this->isTablet())
        {
            return false;
        }
        return true;
    }
}