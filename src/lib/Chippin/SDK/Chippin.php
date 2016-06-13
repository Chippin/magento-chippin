<?php

namespace Chippin\SDK;

class Chippin
{
    private $merchant;

    public function __construct(Merchant $merchant)
    {
        $this->merchant = $merchant;
    }


    public function generateCallbackHash($merchant_order_id)
    {
        return $this->generateHash(sprintf('%s%s', $merchant_order_id, $this->merchant->getId()));
    }

    /**
    * merchant_secret + merchant_id + merchant_order_id + total_amount + duration + currency_code
    *
    */
    public function generateOrderHash($data)
    {
        $hashParts =  array();
        $hashParts[] = $data['merchant_id'];
        $hashParts[] = $data['merchant_order_id'];
        $hashParts[] = $data['total_amount'];
        $hashParts[] = $data['duration'];
        $hashParts[] = $data['currency_code'];

        return $this->generateHash(join($hashParts));
    }

    private function generateHash($string)
    {
        return hash_hmac('sha256', $string, $this->merchant->getSecret());
    }
}
