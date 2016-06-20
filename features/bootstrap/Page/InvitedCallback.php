<?php

namespace Page;

use Behat\Mink\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class InvitedCallback extends Page {
    protected $path = 'chippin/standard/invited?merchant_order_id={merchant_order_id}&hmac={hmac}';
    protected $elements = array(
        'document' => array('xpath' => '/html')
    );

    public function open($params)
    {
        parent::open($params);

        return $this->getPage('Checkout Success Page');
    }

    public function validate()
    {
        $this->getSession()->wait(3000);
        if ($this->getElement('document')->getHtml() !== 'Success') {
           throw new \Exception('Unable to validate response');
        };
    }
}
