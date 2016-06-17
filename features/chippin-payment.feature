Feature: As an organiser of a expensive give purchase
So that I can distribute the payment across a number of people
I want to be able to use the Chippin payment method to checkout

  Scenario: Completed payment shared between two people
    Given I visited product
    And I have added "product" to my basket
    When I complete the checkout
    Then I should be able to select Chippin as a payment method
    And following confirmation I should be directed to the Chippin payment page
