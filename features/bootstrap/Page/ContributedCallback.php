<?php

namespace Page;

use Behat\Mink\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class ContributedCallback extends Page {
    protected $path = 'chippin/standard/contributed?merchant_order_id={merchant_order_id}&hmac={hmac}&first_name={first_name}&last_name={last_name}&email={email}';

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
