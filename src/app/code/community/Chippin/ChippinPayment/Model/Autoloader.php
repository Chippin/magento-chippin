<?php

class Chippin_ChippinPayment_Model_Autoloader
{
    const SDK_LIB_PATH = 'Chippin';
    const CONVERT_CLASS_TO_PATH_REGEX = '#\\\|_(?!.*\\\)#';

    private $_originalAutoloaders;

    public function register()
    {
        $this->_deregisterVarienAutoloaders();
        $this->_registerSDKAutoloader();
        $this->_reregisterVarienAutoloaders();
    }

    private function _registerSDKAutoloader()
    {
        spl_autoload_register(
            function ($className) {
                if(strpos($className, self::SDK_LIB_PATH . '\\') === 0) {
                    include_once preg_replace(self::CONVERT_CLASS_TO_PATH_REGEX, '/', $className) . '.php';
                }
            }
        );

        return $this;
    }


    private function _deregisterVarienAutoloaders()
    {
        $this->_originalAutoloaders = array();

        foreach (spl_autoload_functions() as $callback) {
            if (is_array($callback) && $callback[0] instanceof Varien_Autoload) {
                $this->_originalAutoloaders[] = $callback;
                spl_autoload_unregister($callback);
            }
        }
    }

    private function _reregisterVarienAutoloaders()
    {
        foreach ($this->_originalAutoloaders as $autoloader) {
            spl_autoload_register($autoloader);
        }
    }
}
