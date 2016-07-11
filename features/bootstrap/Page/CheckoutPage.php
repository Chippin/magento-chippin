<?php

namespace Page;

use Behat\Mink\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class CheckoutPage extends Page {
    protected $path = 'checkout/onepage/';

    protected $elements = array(
        'Checkout as guest' => array('xpath' => '//*[@id="login:guest"]'),
        'Continue register' => array('css' => '#onepage-guest-register-button'),
        'Billing first name' => array('xpath' => '//*[@id="billing:firstname"]'),
        'Billing last name' => array('xpath' => '//*[@id="billing:lastname"]'),
        'Billing email' => array('xpath' => '//*[@id="billing:email"]'),
        'Billing street' => array('xpath' => '//*[@id="billing:street1"]'),
        'Billing city' => array('xpath' => '//*[@id="billing:city"]'),
        'Billing postcode' => array('xpath' => '//*[@id="billing:postcode"]'),
        'Billing country' => array('xpath' => '//*[@id="billing:country_id"]'),
        'Billing telephone' => array('xpath' => '//*[@id="billing:telephone"]'),
        'Ship to this address' => array('xpath' => '//*[@id="billing:use_for_shipping_yes"]'),
        'Continue to shipping' => array('css' => '#billing-buttons-container button'),
        'Flat shipping' => array('xpath' => '//*[@id="s_method_flatrate_flatrate"]'),
        'Continue to payment' => array('css' => '#shipping-method-buttons-container button'),
        'Select Chippin' => array('css' => '#p_method_chippinpayment'),
        'Continue to confirm' => array('css' => '#payment-buttons-container button'),
        'Confirm' => array('css' => '#review-buttons-container button')
    );

    public function checkoutAsGuest()
    {
        $this->getElement('Checkout as guest')->click();
        $this->getElement('Continue register')->click();
        $this->getElement('Billing first name')->setValue('Boo');
        $this->getElement('Billing last name')->setValue('Bu');
        $this->getElement('Billing email')->setValue('alistair_stead@me.com');
        $this->getElement('Billing street')->setValue('Street 123');
        $this->getElement('Billing city')->setValue('My town');
        $this->getElement('Billing postcode')->setValue('LE16 7HL');
        $this->getElement('Billing country')->selectOption('United Kingdom');
        $this->getElement('Billing telephone')->setValue('07788107333');
        $this->getElement('Ship to this address')->click();
        $this->getElement('Continue to shipping')->click();
        $this->getSession()->wait(2000);
        //$this->getSession()->wait(
            //20000,
            //"$('checkout-shipping-method-load').children.length"
        //);
        //$this->getElement('Flat shipping')->click();
        $this->getElement('Continue to payment')->click();
        $this->getSession()->wait(
            20000,
            "$('checkout-payment-method-load').children.length"
        );
        $this->getElement('Select Chippin')->click();
        $this->getElement('Continue to confirm')->click();
        $this->getSession()->wait(
            20000,
            "$('checkout-review-load').children.length"
        );
        $this->getElement('Confirm')->click();

        return $this->getPage('Redirect Page');
    }
}
