<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

use Page\ProductPage;
use Page\CartPage;
use Page\CheckoutPage;
use Page\RedirectPage;
use Page\InvitedCallback;
use Page\ContributedCallback;
use Page\CanceledCallback;
use Page\CheckoutSuccessPage;
use Page\RejectedCallback;
use Page\CompletedCallback;
use Page\PaidCallback;
use Page\FailedCallback;
use Page\TimedOutCallback;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements SnippetAcceptingContext
{
    private $product;
    private $cart;
    private $checkout;
    private $redirect;
    private $invited;
    private $contributed;
    private $cancelled;
    private $success;
    private $rejected;
    private $completed;
    private $paid;
    private $failed;
    private $timed_out;

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
        ContributedCallback $contributed,
        CanceledCallback $cancelled,
        CheckoutSuccessPage $success,
        RejectedCallback $rejected,
        CompletedCallback $completed,
        PaidCallback $paid,
        FailedCallback $failed,
        TimedOutCallback $timed_out
    )
    {
        $this->product = $product;
        $this->cart = $cart;
        $this->checkout = $checkout;
        $this->redirect = $redirect;
        $this->invited = $invited;
        $this->contributed = $contributed;
        $this->cancelled = $cancelled;
        $this->success = $success;
        $this->rejected = $rejected;
        $this->completed = $completed;
        $this->paid = $paid;
        $this->failed = $failed;
        $this->timed_out = $timed_out;
    }

	/**
     * @Given :merchant_id and :secret are set
     */
    public function setIdandSecret($merchant_id, $merchant_secret)
    {
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
     * @Then I should be on the :pageName
     */
    public function iShouldBeOnThe($pageName)
    {
        if (!isset($this->$pageName)) {
            throw new \RuntimeException(sprintf('Unrecognised page: "%s".', $pageName));
        }

        if (!$this->$pageName->isOpen()) {
            throw new \RuntimeException('On the wrong page');
        }
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
    }

    /**
     * @When the chippin payment is :action
     */
    public function chippinPaymentCallbackAction($action)
    {
        $params = array(
            'merchant_order_id' => $this->_order_id,
            'hmac' => $this->generateHash(sprintf('%s%s%s', $action, $this->_merchant_id, $this->_order_id), $this->_merchant_secret)
        );
        $this->$action->open($params);
    }

    /**
     * @When contribution is confirmed by :firstName :lastName :email
     */
    public function contributionIsConfirmed($firstName, $lastName, $email)
    {
        $params = array(
            'merchant_order_id' => $this->_order_id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'hmac' => $this->generateHash(
                sprintf('contributed%s%s%s%s%s', $this->_merchant_id, $this->_order_id, $firstName, $lastName, $email),
                $this->_merchant_secret
            )
        );

        $this->contributed->open($params);
    }

    /**
     * @Then the response should be ok
     */
    public function theResponseShouldBeOk()
    {
        $this->assertPageContainsText('Success');
    }

    private function generateHash($string, $merchant_secret)
    {
        $hash =  hash_hmac('sha256', $string, $merchant_secret);

        return $hash;
    }

    /**
     * @Given a testing cookie is set
     */
    public function aTestingCookieIsSet()
    {
        $this->getSession()->getDriver()->setCookie('automated-testing', 'bitter-sequence-garment-serenity');
    }
}
