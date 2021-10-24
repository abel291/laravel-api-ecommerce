<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Specification;

use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::truncate();

        Specification::truncate();


        $categories = [
            'procesadores',
            'board',
            'gpu',
            'ram',
            'almacenamiento',
            'monitores',
            'teclados',
            'mouses',
            'torres',
            'portatiles',
            'sillas',
            'audifonos',
            'fuentes de poder',
            'combos',
            'ensambles',
        ];

        foreach ($categories as $key => $value) {
            echo $value . "\n";
            Category::factory()
                ->has(Specification::factory()->count(6))

                ->create([
                    'name' => ucfirst($value),
                    'slug' => Str::slug($value),
                    'img' => Str::slug($value) . '.png',

                ]);
        }
    }
}
