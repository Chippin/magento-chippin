<?php

namespace Page;

use Behat\Mink\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class CanceledCallback extends Page {
    protected $path = 'chippin/standard/canceled?merchant_order_id={merchant_order_id}&hmac={hmac}';

    public function open($params)
    {
        parent::open($params);

        return $this->getPage('Cart Page');
    }
}
