<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubCategories;

class SubCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subCategories = [
            "T-Shirt",
            "Jeans",
            "Shirt",
            "Trousers"
        ];

        foreach($subCategories as $subCategory) {
            SubCategories::create([
                'category_id' => 1,
                'name' => $subCategory,
            ]);
        }
    }
}
