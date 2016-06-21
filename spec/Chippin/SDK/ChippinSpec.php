<?php

namespace spec\Chippin\SDK;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Chippin\SDK\Merchant;

class ChippinSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new Merchant('100000', '5ba3e1caf655f11b65c2bcef3ec55299a174072a'));
    }

    function it_should_generate_a_consistent_order_hash()
    {
        $this->generateOrderHash(array(
                'merchant_order_id' => '123',
                'total_amount' => '50000',
                'duration' => '72',
                'grace_period' => '8',
                'currency_code' => 'gbp'
            ))
            ->shouldEqual('564b6774653a9d9687aa0a09b73d3c1b91ccf6772d761239c0da45ad3b345169');
    }

    function it_should_generate_a_consitent_callback_hash()
    {
        $this->generateCallbackHash('completed', '123')
            ->shouldEqual('2154aa596b0cd14997ae177fec57b9cdc82236e3869ad2af3ba568fc71997bd9');
    }

    function it_should_generate_a_consitent_contribution_hash()
    {
        $this->generateContributionHash('123', 'Joe', 'Bloggs', 'joe@newcustomer.com')
            ->shouldEqual('5ee6ac84844d85ccfaefde12d3450d4c77856013b7c55fce0c5fb1beef179b88');
    }
}
