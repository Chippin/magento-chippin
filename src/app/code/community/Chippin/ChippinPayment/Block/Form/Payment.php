<?php

class Chippin_ChippinPayment_Block_Form_Payment extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        //$this->setTemplate('chippinpayment/form/chippinpayment.phtml');
        $mark = Mage::getConfig()->getBlockClassName('core/template');
        $mark = new $mark;
        $mark->setTemplate('chippinpayment/form/mark.phtml')
            ->setRedirectMessage(
                Mage::helper('chippinpayment')
                    ->__('You will be redirected to the Chippin website when you place an order.')
            );
        $this->setMethodLabelAfterHtml($mark->toHtml());
        parent::_construct();
    }
}
