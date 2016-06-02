<?php

class Chippin_ChippinPayment_Block_Adminhtml_System_Config_Fieldset_Hint
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'chippinpayment/system/config/fieldset/hint.phtml';

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $oUrl = Mage::getModel('core/url');
        $params = array('_secure' => true);
        // @TODO change this to key => value atray to simplify the template
        $this->setHrefChippinCancel($oUrl->getUrl("chippin/standard/cancel", $params));
        $this->setHrefChippinComplete($oUrl->getUrl("chippin/standard/complete", $params));
        $this->setHrefChippinContribute($oUrl->getUrl("chippin/standard/contribute", $params));
        $this->setHrefChippinFail($oUrl->getUrl("chippin/standard/fail", $params));
        $this->setHrefChippinInvite($oUrl->getUrl("chippin/standard/invite", $params));
        $this->setHrefChippinRejecte($oUrl->getUrl("chippin/standard/rejecte", $params));
        $this->setHrefChippinTimeout($oUrl->getUrl("chippin/standard/timeout", $params));


        $elementOriginalData = $element->getOriginalData();
        if (isset($elementOriginalData['signup_link'])) {
            $this->setSignupLink($elementOriginalData['signup_link']);
        }

        return $this->toHtml();
    }
}
