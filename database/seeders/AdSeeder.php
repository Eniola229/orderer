<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdCategory;
use App\Models\AdBannerSlot;
use Illuminate\Support\Str;

class AdSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Homepage Banner Image',  'type' => 'banner_image',  'description' => 'Static image ad in homepage banner slideshow'],
            ['name' => 'Homepage Banner Video',  'type' => 'banner_video',  'description' => 'Video ad in homepage banner slideshow — premium'],
            ['name' => 'Category Banner Image',  'type' => 'banner_image',  'description' => 'Static image ad in category page banner'],
            ['name' => 'Top Listing',            'type' => 'top_listing',   'description' => 'Appear at top of category and search listings'],
            ['name' => 'Pay Per Order (CPC)',     'type' => 'cpc',           'description' => 'Only charged when buyer places an order via ad'],
        ];

        foreach ($categories as $cat) {
            AdCategory::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [...$cat, 'slug' => Str::slug($cat['name']), 'is_active' => true]
            );
        }

        $slots = [
            [
                'name'          => 'Homepage Hero Banner',
                'location'      => 'homepage_hero',
                'price_per_day' => 25.00,
                'max_ads'       => 5,
                'dimensions'    => '1200x400',
            ],
            [
                'name'          => 'Category Page Banner',
                'location'      => 'category_page',
                'price_per_day' => 12.00,
                'max_ads'       => 5,
                'dimensions'    => '1000x250',
            ],
            [
                'name'          => 'Product Page Sidebar',
                'location'      => 'product_page_sidebar',
                'price_per_day' => 8.00,
                'max_ads'       => 3,
                'dimensions'    => '300x250',
            ],
            [
                'name'          => 'Search Results Banner',
                'location'      => 'search_results',
                'price_per_day' => 15.00,
                'max_ads'       => 3,
                'dimensions'    => '1000x150',
            ],
        ];

        foreach ($slots as $slot) {
            AdBannerSlot::firstOrCreate(
                ['slug' => Str::slug($slot['name'])],
                [...$slot, 'slug' => Str::slug($slot['name']), 'is_active' => true]
            );
        }
    }
}