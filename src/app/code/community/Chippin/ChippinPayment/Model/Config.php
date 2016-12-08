<?php

class Chippin_ChippinPayment_Model_Config {

    const METHOD_CODE = 'chippinpayment';

    public function getEndpointUrl()
    {
        return (Mage::getStoreConfig('payment/chippinpayment/sandbox'))? Mage::getStoreConfig('payment/chippinpayment/sandbox_endpoint') : Mage::getStoreConfig('payment/chippinpayment/live_endpoint');
    }

    public function getMerchantId()
    {
        return Mage::getStoreConfig('payment/chippinpayment/merchant_id');
    }

    public function getSecret()
    {
        return Mage::getStoreConfig('payment/chippinpayment/secret');
    }

    public function getDuration()
    {
        return Mage::getStoreConfig('payment/chippinpayment/duration');
    }

    public function getGracePeriod()
    {
        return Mage::getStoreConfig('payment/chippinpayment/grace_period');
    }

    public function getTitle()
    {
        return Mage::getStoreConfig('payment/chippinpayment/title');
    }

    public function getDescription()
    {
        return Mage::getStoreConfig('payment/chippinpayment/description');
    }

    public function getRedirectMessage()
    {
        return Mage::getStoreConfig('payment/chippinpayment/redirect_message');
    }

    public function getNewOrderStatus()
    {
        return Mage::getStoreConfig('payment/chippinpayment/order_status');
    }

}
