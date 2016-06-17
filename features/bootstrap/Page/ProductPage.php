<?php

namespace Page;

use Behat\Mink\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class ProductPage extends Page {
    protected $path = '/men/shirts/plaid-cotton-shirt-473.html';

    protected $elements = array(
        'Color' => array('css' => 'li.option-charcoal'),
        'Size' => array('css' => 'li.option-xl'),
        'Add to cart' => array('css' => 'div.add-to-cart-buttons button')
    );

    public function addToCart()
    {
        $this->getElement('Color')->click();
        $this->getElement('Size')->click();
        $this->getElement('Add to cart')->click();

        return $this->getPage('Cart Page');
    }
}
