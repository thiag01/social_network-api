<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\ProductsModel;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         // $this->call(ProductsTableSeeder::class);
         ProductsModel::factory(10)->create();
    }
}
