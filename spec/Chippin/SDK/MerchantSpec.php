<?php

namespace spec\Chippin\SDK;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MerchantSpec extends ObjectBehavior
{
    const ID = 1;
    const SECRET = '5ba3e1caf655f11b65c2bcef3ec55299a174072a';

    function let()
    {
        $this->beConstructedWith(self::ID, self::SECRET);
    }

    function it_should_throw_an_exception_if_missing_args()
    {
        $this->beConstructedWith(null, null);
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }

    function it_exposes_id_through_a_getter()
    {
        $this->getId()->shouldEqual(self::ID);
    }

    function it_exposes_secret_through_a_getter()
    {
        $this->getSecret()->shouldEqual(self::SECRET);
    }
}
