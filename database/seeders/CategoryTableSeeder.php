<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categories;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            "Clothing",
            "Grocery",
            "Electronics",
            "Furnitures"
        ];

        foreach($categories as $category) {
            Categories::create([
                'name' => $category,
            ]);
        }
    }
}
