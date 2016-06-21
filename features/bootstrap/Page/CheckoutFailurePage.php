<?php

namespace Page;

use Behat\Mink\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class CheckoutFailurePage extends Page {
    protected $path = 'checkout/onepage/failure/';
}
