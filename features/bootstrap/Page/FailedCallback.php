<?php

namespace Page;

use Behat\Mink\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class FailedCallback extends Page {
    protected $path = 'chippin/standard/failed?merchant_order_id={merchant_order_id}&hmac={hmac}';
}
