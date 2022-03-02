<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories =[
            ['name' => 'Clothes','active' => true,'created_at'=> now(),'updated_at'=> now()],
            ['name' => 'Shoes' , 'active' => true,'created_at'=> now(),'updated_at'=> now()],
            ['name' => 'Watches', 'active' => true,'created_at'=> now(),'updated_at'=> now()],
            ['name' => 'Electronics', 'active' => true,'created_at'=> now(),'updated_at'=> now()],
        ];

        Category::insert($categories);

        $image = ['file_name' => '01.jpg', 'file_type' => 'image/jpg', 'file_size' => rand(100, 900), 'file_status' => true, 'file_sort' => 0];

        Category::all()->each(function ($category) use ($image){

            $category->media()->create($image);

        });

    }
}