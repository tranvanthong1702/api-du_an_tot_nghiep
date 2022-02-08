<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'cate_id'=>Category::all()->random()->id,
            'name'=>$this->faker->name(),
            'image'=>'https://picsum.photos/500/500',
            'price'=> rand(100, 500),
            'sale'=>rand(0, 100),
            'status'=>array_rand([0,1]),
            'expiration_date'=>$this->faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = 'Asia/Ho_Chi_Minh'), // DateTime('2003-03-15 02:00:49', 'Africa/Lagos'),
            'desc_short'=>$this->faker->text($maxNbChars = 200),
            'description'=>$this->faker->text($maxNbChars = 200),
        ];
    }
}
