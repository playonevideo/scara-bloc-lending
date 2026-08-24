<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Unelte', 'icon' => '🔧'],
            ['name' => 'Bricolaj', 'icon' => '🔨'],
            ['name' => 'Casă și grădină', 'icon' => '🏡'],
            ['name' => 'Electrocasnice', 'icon' => '🔌'],
            ['name' => 'Copii', 'icon' => '🧸'],
            ['name' => 'Sport', 'icon' => '⚽'],
            ['name' => 'Cărți', 'icon' => '📚'],
            ['name' => 'Evenimente', 'icon' => '🎉'],
            ['name' => 'Auto', 'icon' => '🚗'],
            ['name' => 'Diverse', 'icon' => '📦'],
        ];

        foreach ($categories as $index => $category) {
            Category::create([
                ...$category,
                'slug' => \Illuminate\Support\Str::slug($category['name']),
                'sort_order' => $index,
            ]);
        }
    }
}
