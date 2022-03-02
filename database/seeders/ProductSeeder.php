<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $faker = Factory::create();

        $categories = Category::pluck('id');

        for ($i = 1; $i <= 100; $i++) {
            $products[] = [
                'name'                  => $faker->sentence(2, true),
                'discription'           => $faker->paragraph,
                'price'                 => $faker->numberBetween(5, 200),
                'category_id'           => $categories->random(),
                'active'                => true,
                'created_at'            => now(),
                'updated_at'            => now(),
            ];
        }

        $chunks = array_chunk($products, 100);
        foreach ($chunks as $chunk) {
            Product::insert($chunk);
        }

        $images[] = ['file_name' => '01.jpg', 'file_type' => 'image/jpg', 'file_size' => rand(100, 900), 'file_status' => true, 'file_sort' => 0];
        $images[] = ['file_name' => '02.jpg', 'file_type' => 'image/jpg', 'file_size' => rand(100, 900), 'file_status' => true, 'file_sort' => 0];
        $images[] = ['file_name' => '03.jpg', 'file_type' => 'image/jpg', 'file_size' => rand(100, 900), 'file_status' => true, 'file_sort' => 0];
        $images[] = ['file_name' => '04.jpg', 'file_type' => 'image/jpg', 'file_size' => rand(100, 900), 'file_status' => true, 'file_sort' => 0];
        $images[] = ['file_name' => '05.jpg', 'file_type' => 'image/jpg', 'file_size' => rand(100, 900), 'file_status' => true, 'file_sort' => 0];
        $images[] = ['file_name' => '06.jpg', 'file_type' => 'image/jpg', 'file_size' => rand(100, 900), 'file_status' => true, 'file_sort' => 0];
        $images[] = ['file_name' => '07.jpg', 'file_type' => 'image/jpg', 'file_size' => rand(100, 900), 'file_status' => true, 'file_sort' => 0];
        $images[] = ['file_name' => '08.jpg', 'file_type' => 'image/jpg', 'file_size' => rand(100, 900), 'file_status' => true, 'file_sort' => 0];


        Product::all()->each(function ($product) use ($images){

            $product->media()->createMany(Arr::random($images,rand(2,3)));

        });

    }
}