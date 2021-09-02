<?php

namespace Database\Factories;

use App\Models\ProductsModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator;
use Illuminate\Support\Str;

class ProductsModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductsModel::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'name' => $this->faker->lastname(),
            'price' => $this->faker->numberBetween($min = 1000, $max = 9000),
            'description'=> $this->faker->sentence(10),
            'slug' => Str::random(10),
            'data' => date("Y-m-d")
        ];
    }
}
