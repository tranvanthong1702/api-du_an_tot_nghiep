<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'paymentID'=>rand(11111111,99999999),
            'requestID'=>rand(11111111,99999999),
            'transID'=>rand(111111,999999),
            'amount'=>rand(50,200),
            'resultCode'=>array_rand([0,900]),
            'message'=>'Thanh toán thành công',
        ];
    }
}
