<?php


namespace App\Mixins;


class StringMixins
{

    /**
     * Formats phone number to 234x0xxxxxxx
     *
     * @param $phoneNumber
     *
     * @return mixed
     */
    public function prefix234ToPhoneNumber()
    {
        return function ($phoneNumber){
            //check if its a gsm number that begins with zero
            if (strlen($phoneNumber) == 11 && substr($phoneNumber, 0, 1) == '0') {
                $phoneNumber = '234' . substr($phoneNumber, 1);

                return $phoneNumber;
            } else {
                return $phoneNumber;
            }
        };
    }
}