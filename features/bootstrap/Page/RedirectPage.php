<?php

namespace Page;

use Behat\Mink\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class RedirectPage extends Page {
    protected $path = 'chippin/standard/redirect';

    protected $elements = array(
        'merchant_order_id' => array('xpath' => '//*[@id="merchant_order_id"]')
    );

    public function extractOrderId()
    {
        $this->getSession()->wait(3000);
        return $this->getElement('merchant_order_id')->getValue();
    }
}
