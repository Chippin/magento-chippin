<?php

namespace Chippin\SDK;

class Chippin
{
    private $merchant;

    // public function __construct(Merchant $merchant)
    // {
    //     $this->merchant = $merchant;
    // }

    /**
    * merchant_secret + merchant_id + merchant_order_id + total_amount + duration + currency_code
    *
    */
    public function generateOrderHash($data, $secret)
    {
        $hashParts =  array();
        $hashParts[] = $data['merchant_id'];
        $hashParts[] = $data['merchant_order_id'];
        $hashParts[] = $data['total_amount'];
        $hashParts[] = $data['duration'];
        $hashParts[] = $data['currency_code'];

        return $this->generateHash(join($hashParts), $secret);
    }

    private function generateHash($string, $secret)
    {
        return hash_hmac('sha256', $string, $secret);
    }
}
