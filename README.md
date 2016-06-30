# Magento 1.x Chippin Payment Module

Chippin is a shared payment gateway that allows you, the merchant, the ability to offer group buying for a single order. This means that a customer can split the cost of a purchase with their friends/family.

At the end of your checkout process, you redirect to Chippin with the basic details of the customer and your own unique order reference. At this point, the customer can invite their friends and family to chip in (to chip in as a verb / a ‘chippin’ as a noun / Chippin as the business) for the item(s) they are purchasing.

Chippin is a simple ‘plug-in’ that integrates quickly and seamlessly into any merchant’s online checkout. It enables consumers to easily split the cost of an online transaction safely and securely to their own and the merchant’s benefit.

## Install

To install the module you should upload the supplied ChippinPayment-x.x.x.tgz using the Magento
Connect Manager interface. Alternatively if you are familiar with Magento module development you can extract the contents of the package and place them
into the correct locations within your Magento install directory.

Once you have installed the module it will provide a number of configuration options in your Magento
administration interface alongside all other installed payment modules. It will display the
callback URLS that you will need to enter in the [Chippin system configuration panel](https://chippin.co.uk/admin). These URLs allow Chippin to update the status of the order or route the Instigator or Contributors to appropriate content during all the stages of the Chippin payment.

During initial install you can use the sandbox functionality to validate that all functionality is
working the way that you expected. In sandbox mode you can use fake card details from the list of [Stripe test card](https://stripe.com/docs/testing). **Ensure that you turn off the use of sandbox before you make this
module available to customers.**

**You can also configure the URL endpoints of Chippin. You should only do this when directed by your
account manager at Chippin or it may result in you not capturing the money correctly from your
customers.**

## Functionality

The module will create a number of specific order statuses that will be used to track the progress of
the order through the extended asynchronous payment flow with Chippin. You should ensure that you
correctly filter orders that use the Chippin payment method when passing the details to your fulfillment system. You should only consider orders with the status label of "Chippin Paid" status code of "chippin_paid" to be paid in full and ready to dispatch.

During the checkout flow the Instigator of the order will be redirected at the end of checkout to
the Chippin system to submit part payment and to invite others to contribute. Contributors will be
sent invitations via email and following entering payment details to make their contribution will be
redirected to your Magento store and the checkout/onepage/success page with the context of the
order. This allows you to add additional merchandising blocks to up-sell and cross-sell to the
contributors.

## System Configuration Options

* Marchant Id (1) - Your unique merchant ID to connect to your Chippin account available at [Chippin](https://chippin.co.uk/admin)
* Secret (5ba3e1caf655f11b65c2bcef3ec55299a174072a) - A secret token used to authenticate the messages between Magento and the Chippin Payment gateway available at [Chippin](https://chippin.co.uk/admin).
* Use Sandbox - Enable the sandbox for initial testing but ensure this is set to "No" on your live
  environment.
* Sandbox API URL - The default value should be used unless directed to change this value.
* Live API URL - The default value should be used unless directed to change this value.
* Sort Order - The integer value entered here allows you to control the order in which all the
  payment options are displayed within the Magento checkout.
* Title - The title of the payment method in the checkout.
* Duration - This is the number of hours you wish the ‘chippin’ to last before it times out.
* Grace Period - This is the number of hours you wish to give the user to complete a chippin after the timeout period.
