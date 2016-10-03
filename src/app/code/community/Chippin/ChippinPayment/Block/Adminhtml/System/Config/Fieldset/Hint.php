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
        $storeId = Mage::app()
            ->getWebsite(true)
            ->getDefaultGroup()
            ->getDefaultStoreId();

        //$oUrl = Mage::getModel('core/url');
		$oUrl = Mage::app()->getStore($storeId);
        $params = array('_secure' => true, '_store_to_url' => false, '_type' => 'direct_link');
        $integrationUrls = array(
            'Canceled' => $oUrl->getUrl("chippin/standard/canceled", $params),
            'Completed' => $oUrl->getUrl("chippin/standard/completed", $params),
            'Contributed' => $oUrl->getUrl("chippin/standard/contributed", $params),
            'Failed' => $oUrl->getUrl("chippin/standard/failed", $params),
            'Invited' => $oUrl->getUrl("chippin/standard/invited", $params),
            'Paid' => $oUrl->getUrl("chippin/standard/paid", $params),
            'Rejected' => $oUrl->getUrl("chippin/standard/rejected", $params),
            'Timed Out' => $oUrl->getUrl("chippin/standard/timedout", $params)
        );

        $this->setIntegrationUrls($integrationUrls);

        $elementOriginalData = $element->getOriginalData();
        if (isset($elementOriginalData['signup_link'])) {
            $this->setSignupLink($elementOriginalData['signup_link']);
        }

        return $this->toHtml();
    }
}
