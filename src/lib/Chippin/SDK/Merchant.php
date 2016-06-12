<?php

namespace Chippin\SDK;

class Merchant
{
    private $id;
    private $secret;

    public function __construct($id = null, $secret = null)
    {
        if (is_null($id)) {
            throw new \InvalidArgumentException('Missing required parameter Merchant ID');
        }
        if (is_null($secret)) {
            throw new \InvalidArgumentException('Missing required parameter Merchant Secret');
        }
        $this->id = $id;
        $this->secret = $secret;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSecret()
    {
        return $this->secret;
    }
}
