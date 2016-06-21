<?php

namespace Page;

use Behat\Mink\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\UnexpectedPageException;

class CompletedCallback extends Page {
    protected $path = 'chippin/standard/completed?merchant_order_id={merchant_order_id}&hmac={hmac}';

    protected function verifyUrl(array $urlParameters = array())
    {
        $page = $this->getPage('Checkout Success Page');
        if ($this->getDriver()->getCurrentUrl() !== $page->getUrl()) {
            throw new UnexpectedPageException(
                sprintf('Expected to be on "%s" but found "%s" instead', $page->getUrl($urlParameters), $this->getDriver()->getCurrentUrl())
            );
        }
    }
}
