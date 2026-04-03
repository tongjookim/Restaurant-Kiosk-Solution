<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $burgerId = Category::where('name', 'Burgers')->first()->id;
        $drinkId = Category::where('name', 'Drinks')->first()->id;

        $menus = [
            // Burgers
            ['category_id' => $burgerId, 'name' => 'Chicken Burger', 'price' => 5500, 'image_url' => '/images/chicken_burger.jpg', 'is_available' => true],
            ['category_id' => $burgerId, 'name' => 'Beef Burger', 'price' => 6500, 'image_url' => '/images/beef_burger.jpg', 'is_available' => true],
            ['category_id' => $burgerId, 'name' => 'Double Cheese Burger', 'price' => 7500, 'image_url' => '/images/double_burger.jpg', 'is_available' => true],
            // Drinks
            ['category_id' => $drinkId, 'name' => 'Cola', 'price' => 2000, 'image_url' => '/images/cola.jpg', 'is_available' => true],
            ['category_id' => $drinkId, 'name' => 'Sprite', 'price' => 2000, 'image_url' => '/images/sprite.jpg', 'is_available' => true],
        ];

        DB::table('menus')->insert($menus);
    }
}
