Feature: As an organiser of a expensive gift purchase
So that I can distribute the payment across a number of people
I want to be able to use the Chippin payment method to checkout

  Background:
    Given "7" and "52085619dc8b8ec818f1513cd170ab1664f685d3" are set
    And I visited product
    And a testing cookie is set
    And I have added "product" to my basket
    And I complete the checkout

  Scenario: Contributors invited
    When the chippin payment is "invited"
    Then the response should be ok

  Scenario: Contributor makes contribution
    Given the chippin payment is "invited"
    When contribution is confirmed by 'Joe' 'Bloggs' 'joe@newcustomer.com'
    Then I should be on the "chippin_contributed"

  Scenario: Chippin rejects payment
    Given the chippin payment is "invited"
    When the chippin payment is "rejected"
    Then I should be on the "chippin_declined"

  Scenario: Chippin payment is completed
    Given the chippin payment is "invited"
    When the chippin payment is "completed"
    Then I should be on the "success"

  Scenario: Chippin payment is paid
    Given the chippin payment is "invited"
    When the chippin payment is "paid"
    Then the response should be ok

  Scenario: Chippin payment fails
    Given the chippin payment is "invited"
    When the chippin payment is "failed"
    Then the response should be ok

  Scenario: Canceled by Instigator
    When the chippin payment is "cancelled"
    Then I should be on the "cart"

  Scenario: Chippin payment times out
    Given the chippin payment is "invited"
    When the chippin payment is "timed_out"
    Then the response should be ok


