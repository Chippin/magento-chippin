<?php

namespace Page;

use Behat\Mink\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class RejectedCallback extends Page {
    protected $path = 'chippin/standard/rejected?merchant_order_id={merchant_order_id}&hmac={hmac}';

    protected function verifyUrl(array $urlParameters = array())
    {
        $page = $this->getPage('Chippin Declined Page');
        if ($this->getDriver()->getCurrentUrl() !== $page->getUrl()) {
            throw new UnexpectedPageException(
                sprintf('Expected to be on "%s" but found "%s" instead', $page->getUrl($urlParameters), $this->getDriver()->getCurrentUrl())
            );
        }
    }

}
