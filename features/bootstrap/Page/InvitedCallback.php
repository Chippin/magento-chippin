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

    protected function verifyUrl(array $urlParameters = array())
    {
        $successPage = $this->getPage('Checkout Success Page');
        if ($this->getDriver()->getCurrentUrl() !== $successPage->getUrl()) {
            throw new UnexpectedPageException(
                sprintf('Expected to be on "%s" but found "%s" instead', $successPage->getUrl($urlParameters), $this->getDriver()->getCurrentUrl())
            );
        }
    }
}
