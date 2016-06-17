<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Page\ProductPage;
use Page\CartPage;
use Page\CheckoutPage;


/**
 * Defines application features from the specific context.
 */
class FeatureContext implements SnippetAcceptingContext
{
    private $product;
    private $cart;
    private $checkout;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(ProductPage $product, CartPage $cart, CheckoutPage $checkout)
    {
        $this->product = $product;
        $this->cart = $cart;
        $this->checkout = $checkout;
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
        $this->checkout->checkoutAsGuest();
    }

    /**
     * @Then I should be able to select Chippin as a payment method
     */
    public function iShouldBeAbleToSelectChippinAsAPaymentMethod()
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
}
