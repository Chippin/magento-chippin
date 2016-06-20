<?php

namespace Page;

use Behat\Mink\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class ContributedCallback extends Page {
    protected $path = 'chippin/standard/contributed?merchant_order_id={merchant_order_id}&hmac={hmac}&first_name={first_name}&last_name={last_name}&email={email}';

    public function open($params)
    {
        parent::open($params);

        return $this->getPage('Cart Page');
    }
}
