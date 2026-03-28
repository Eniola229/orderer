<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'icon' => 'feather-smartphone',
                'commission_rate' => 5.00,
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Fashion',
                'icon' => 'feather-shirt',
                'commission_rate' => 8.00,
                
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Home & Living',
                'icon' => 'feather-home',
                'commission_rate' => 7.00,
                
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Beauty & Personal Care',
                'icon' => 'feather-heart',
                'commission_rate' => 10.00,
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'Sports & Outdoors',
                'icon' => 'feather-activity',
                'commission_rate' => 8.00,
                'is_active' => true,
                'sort_order' => 5
            ],
            [
                'name' => 'Books & Stationery',
                'icon' => 'feather-book',
                'commission_rate' => 6.00,
                'is_active' => true,
                'sort_order' => 6
            ],
            [
                'name' => 'Toys & Games',
                'icon' => 'feather-gamepad',
                'commission_rate' => 8.00,
                'is_active' => true,
                'sort_order' => 7
            ],
            [
                'name' => 'Food & Beverages',
                'icon' => 'feather-coffee',
                'commission_rate' => 10.00,
                'is_active' => true,
                'sort_order' => 8
            ],
            [
                'name' => 'Health & Wellness',
                'icon' => 'feather-heart',
                'commission_rate' => 9.00,
                'is_active' => true,
                'sort_order' => 9
            ],
            [
                'name' => 'Automotive',
                'icon' => 'feather-truck',
                'commission_rate' => 7.00,
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'name' => 'Baby & Kids',
                'icon' => 'feather-baby',
                'commission_rate' => 8.00,
                'is_active' => true,
                'sort_order' => 11
            ],
            [
                'name' => 'Pet Supplies',
                'icon' => 'feather-paw',
                'commission_rate' => 7.00,
                'is_active' => true,
                'sort_order' => 12
            ],
            [
                'name' => 'Jewelry & Watches',
                'icon' => 'feather-gift',
                'commission_rate' => 12.00,
                'is_active' => true,
                'sort_order' => 13
            ],
            [
                'name' => 'Computers & Accessories',
                'icon' => 'feather-monitor',
                'commission_rate' => 6.00,
                'is_active' => true,
                'sort_order' => 14
            ],
            [
                'name' => 'Mobile & Tablets',
                'icon' => 'feather-smartphone',
                'commission_rate' => 5.00,
                'is_active' => true,
                'sort_order' => 15
            ],
            [
                'name' => 'Cameras & Photography',
                'icon' => 'feather-camera',
                'commission_rate' => 8.00,
                'is_active' => true,
                'sort_order' => 16
            ],
            [
                'name' => 'Music & Instruments',
                'icon' => 'feather-music',
                'commission_rate' => 9.00,
                'is_active' => true,
                'sort_order' => 17
            ],
            [
                'name' => 'Tools & DIY',
                'icon' => 'feather-tool',
                'commission_rate' => 7.00,
                'is_active' => true,
                'sort_order' => 18
            ],
            [
                'name' => 'Garden & Outdoor',
                'icon' => 'feather-sun',
                'commission_rate' => 7.00,
                'is_active' => true,
                'sort_order' => 19
            ],
            [
                'name' => 'Art & Craft',
                'icon' => 'feather-feather',
                'commission_rate' => 10.00,
                'is_active' => true,
                'sort_order' => 20
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'icon' => $category['icon'],
                'commission_rate' => $category['commission_rate'],
                'is_active' => $category['is_active'],
                'sort_order' => $category['sort_order'],
            ]);
        }
    }
}