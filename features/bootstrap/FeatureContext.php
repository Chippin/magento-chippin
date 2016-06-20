<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Page\ProductPage;
use Page\CartPage;
use Page\CheckoutPage;
use Page\RedirectPage;
use Page\InvitedCallback;
use Page\CanceledCallback;
use Page\CheckoutSuccessPage;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements SnippetAcceptingContext
{
    private $product;
    private $cart;
    private $checkout;
    private $redirect;
    private $invited;
    private $canceled;
    private $success;

    private $_merchant_id;
    private $_merchant_secret;
    private $_order_id;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(
        ProductPage $product,
        CartPage $cart,
        CheckoutPage $checkout,
        RedirectPage $redirect,
        InvitedCallback $invited,
        CanceledCallback $canceled,
        CheckoutSuccessPage $success
    )
    {
        $this->product = $product;
        $this->cart = $cart;
        $this->checkout = $checkout;
        $this->redirect = $redirect;
        $this->invited = $invited;
        $this->canceled = $canceled;
        $this->success = $success;
    }

	/**
     * @Given :merchant_id and :secret are set
     */
    public function andAreSet($merchant_id, $merchant_secret)
    {
        echo sprintf("Setting merchant_secret to: %s", $merchant_secret);
        $this->_merchant_id = $merchant_id;
        $this->_merchant_secret = sprintf('%s', $merchant_secret);
    }

	/**
     * @Given /^(?:|I )visited (?:|the )(?P<pageName>.*?)$/
     */
    public function iVisitedThePage($pageName)
    {
        if (!isset($this->$pageName)) {
            throw new \RuntimeException(sprintf('Unrecognised page: "%s".', $pageName));
        }

        $this->$pageName->open();
    }

    /**
     * @Given I have added :arg1 to my basket
     */
    public function iHaveAddedToMyBasket($arg1)
    {
        $this->product->addToCart()->checkout();
    }

    /**
     * @When I complete the checkout
     */
    public function iCompleteTheCheckout()
    {
        $this->_order_id = $this->checkout->checkoutAsGuest()->extractOrderId();
    }

    /**
     * @When users are invited
     */
    public function usersAreInvited()
    {
        $params = array(
            'merchant_order_id' => $this->_order_id,
            'hmac' => $this->generateHash(sprintf('%s%s%s', 'invited', $this->_merchant_id, $this->_order_id), $this->_merchant_secret)
        );
        $this->invited->open($params);
        $this->success->isOpen();
    }

	/**
     * @When the Instigator cancels the transaction
     */
    public function theInstigatorCancelsTheTransaction()
    {
         $params = array(
            'merchant_order_id' => $this->_order_id,
            'hmac' => $this->generateHash(sprintf('%s%s%s', 'cancelled', $this->_merchant_id, $this->_order_id), $this->_merchant_secret)
        );
        $this->canceled->open($params)->isValid();
    }

	/**
     * @Then I should be able to select Chippin as a payment method
     */
    public function iShouldBeAbleToSelectChippinAsAPaymentMethod2()
    {
        throw new PendingException();
    }

    /**
     * @Then following confirmation I should be directed to the Chippin payment page
     */
    public function followingConfirmationIShouldBeDirectedToTheChippinPaymentPage()
    {
        throw new PendingException();
    }


    public function generateHash($string, $merchant_secret)
    {
        echo sprintf("Hash generated from string: %s secret: %s \n", $string, $merchant_secret);
        $hash =  hash_hmac('sha256', $string, $merchant_secret);
        echo sprintf("Hash: %s \n", $hash);
        return $hash;
    }
}
