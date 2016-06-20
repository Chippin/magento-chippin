<?php

namespace spec\Chippin\SDK;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CallbackSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Chippin\SDK\Callback');
    }
}
