<?php
class Chippin_ChippinPayment_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $_config;

    public function getDescription()
    {
        return $this->__($this->getConfig()->getDescription());
    }

    public function getRedirectMessage()
    {
        return $this->__($this->getConfig()->getRedirectMessage());
    }

    /**
     * Config instance getter
     * @return Chippin_ChippinPayment_Model_Config
     */
    private function getConfig()
    {
        if (null === $this->_config) {
            $this->_config = Mage::getModel('chippinpayment/config');
        }

        return $this->_config;
    }

    public function getExtensionVersion()
    {
        return (string) Mage::getConfig()->getNode()->modules->Chippin_ChippinPayment->version;
    }

}
