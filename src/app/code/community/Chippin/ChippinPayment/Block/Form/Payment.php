<?php

class Chippin_ChippinPayment_Block_Form_Payment extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $mark = Mage::getConfig()->getBlockClassName('core/template');
        $mark = new $mark;
        $mark->setTemplate('chippinpayment/form/mark.phtml')
            ->setDescription(Mage::helper('chippinpayment')->getDescription())
            ->setRedirectMessage(Mage::helper('chippinpayment')->getRedirectMessage());
        $this->setMethodLabelAfterHtml($mark->toHtml());
        parent::_construct();
    }
}
