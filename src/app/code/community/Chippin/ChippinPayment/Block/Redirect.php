<?php

class Chippin_ChippinPayment_Block_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $method = Mage::getModel('chippinpayment/payment_method');

        $form = new Varien_Data_Form();
        $form->setAction($method->getConfig()->getEndpointUrl())
            ->setId('chippin_standard_checkout')
            ->setName('chippin_standard_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);

        foreach ($method->getCheckoutFormFields() as $field => $value) {
            if (is_array($value)) {
                foreach ($value as $index => $product) {
                    foreach ($product as $productLabel => $productValue) {
                        $productFieldId = sprintf("chippin-product-%s-%s", $index, $productLabel);
                        $productFieldName = sprintf("%s[][%s]", $field, $productLabel);
                        $form->addField($productFieldId, 'hidden', array('name' => $productFieldName, 'value' => $productValue));
                    }
                }
            } else {
                $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
            }
        }

        $idSuffix = Mage::helper('core')->uniqHash();
        $submitButton = new Varien_Data_Form_Element_Submit(array(
            'value'    => $this->__('Click here if you are not redirected within 10 seconds...'),
        ));
        $buttonId = "submit_to_chippin_button_{$idSuffix}";
        $submitButton->setId($buttonId);

        $form->addElement($submitButton);

        $html = '<html><body>';
        $html.= $this->__('You will be redirected to the Chippin website in a few seconds.');
        $html.= $form->toHtml();
        if (Mage::getModel('core/cookie')->get('automated-testing') !== 'bitter-sequence-garment-serenity') {
            $html.= '<script type="text/javascript">document.getElementById("chippin_standard_checkout").submit();</script>';
        }
        $html.= '</body></html>';

        return $html;
    }
}
