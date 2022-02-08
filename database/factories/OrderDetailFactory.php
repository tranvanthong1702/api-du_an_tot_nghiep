<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
        'order_id' =>Order::all()->random()->id,
        'product_id'=>rand(1,10),
        'standard_price'=>rand(20,100),
        'standard_name'=>$this->faker->name(),
        'quantity'=>rand(1,5),
        ];
    }
}
