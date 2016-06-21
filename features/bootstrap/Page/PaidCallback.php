<?php

namespace Page;

use Behat\Mink\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class PaidCallback extends Page {
    protected $path = 'chippin/standard/paid?merchant_order_id={merchant_order_id}&hmac={hmac}';
}
