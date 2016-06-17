<?php

namespace Page;

use Behat\Mink\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class CartPage extends Page {
    protected $path = 'checkout/cart/';

    protected $elements = array(
        'Proceed to Checkout' => array('css' => 'button.btn-proceed-checkout.btn-checkout')
    );

    public function checkout()
    {
        $this->getElement('Proceed to Checkout')->click();
    }
}
