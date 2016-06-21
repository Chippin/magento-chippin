<?php

namespace Page;

use Behat\Mink\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\UnexpectedPageException;

class InvitedCallback extends Page {
    protected $path = 'chippin/standard/invited?merchant_order_id={merchant_order_id}&hmac={hmac}';
    protected $elements = array(
        'document' => array('xpath' => '/html')
    );
}
