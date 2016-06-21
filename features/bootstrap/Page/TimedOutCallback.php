<?php

namespace Page;

use Behat\Mink\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class TimedOutCallback extends Page {
    protected $path = 'chippin/standard/timedout?merchant_order_id={merchant_order_id}&hmac={hmac}';
}
