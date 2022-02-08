<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "user_id"=>User::all()->random()->id,
            "code_orders" => rand(11111111, 99999999),
            "total_price" => rand(100, 500),
            "customer_name" => $this->faker->name(),
            "customer_email" => $this->faker->unique()->safeEmail(),
            "customer_phone" => $this->faker->tollFreePhoneNumber,
            "customer_address" => $this->faker->streetAddress,
            "transportation_costs" => rand(10, 100),
            "payments" => array_rand([0, 1]),
        ];
    }
}
