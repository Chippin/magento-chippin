<?php

namespace Page;

use Behat\Mink\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class CheckoutSuccessPage extends Page {
    protected $path = 'chechout/onepage/success';
}
