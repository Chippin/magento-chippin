Feature: As an organiser of a expensive give purchase
So that I can distribute the payment across a number of people
I want to be able to use the Chippin payment method to checkout

  Scenario: Completed payment shared between two people
    Given "7" and "52085619dc8b8ec818f1513cd170ab1664f685d3" are set
    And I visited product
    And I have added "product" to my basket
    When I complete the checkout
    And users are invited
    Then I should be able to select Chippin as a payment method
    And following confirmation i should be directed to the Chippin payment page

  Scenario: Cancel after confirmation
    Given "7" and "52085619dc8b8ec818f1513cd170ab1664f685d3" are set
    And I visited product
    And i have added "product" to my basket
    When I complete the checkout
    And the Instigator cancels the transaction
    Then I should be able to select Chippin as a payment method
    And following confirmation i should be directed to the Chippin payment page
