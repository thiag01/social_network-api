<?php

namespace Database\Seeders;
use App\Models\ProductsModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        ProductsModel::insert([
            'name' => Str::random(10),
            'price' => 20.000000,
            'description' => Str::random(10),
            'slug' => Str::random(10),
            'data' => date("Y-m-d")
        ]);
    }
}
