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
        $this->setHrefChippinCancelled($oUrl->getUrl("chippin/standard/cancelled", $params));
        $this->setHrefChippinCompleted($oUrl->getUrl("chippin/standard/completed", $params));
        $this->setHrefChippinContributed($oUrl->getUrl("chippin/standard/contributed", $params));
        $this->setHrefChippinFailed($oUrl->getUrl("chippin/standard/failed", $params));
        $this->setHrefChippinInvited($oUrl->getUrl("chippin/standard/invited", $params));
        $this->setHrefChippinRejected($oUrl->getUrl("chippin/standard/rejected", $params));
        $this->setHrefChippinTimedout($oUrl->getUrl("chippin/standard/timedout", $params));


        $elementOriginalData = $element->getOriginalData();
        if (isset($elementOriginalData['signup_link'])) {
            $this->setSignupLink($elementOriginalData['signup_link']);
        }

        return $this->toHtml();
    }
}
