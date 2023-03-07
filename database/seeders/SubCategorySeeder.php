<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories= [
            ['name'=>'Furniture','image'=>'https://images.freeimages.com/images/large-previews/8a6/ac-repair-santa-barbara-1640064.jpg']  ,
            ['name'=>'Mobile','image'=>'https://images.unsplash.com/photo-1546054454-aa26e2b734c7?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxzZWFyY2h8NHx8bW9iaWxlfGVufDB8fDB8fA%3D%3D&auto=format&fit=crop&w=600&q=60']  ,
            ['name'=>'Electronic','image'=>'https://images.freeimages.com/images/large-previews/8a6/ac-repair-santa-barbara-1640064.jpg']  ,
        ];
        foreach ($categories as $key=> $category){
            $cat= SubCategory::create([
                'name'=>$category['name'],
                'category_id'=>$key+1
            ]);
            $cat->addMediaFromUrl($category['image'])
                ->toMediaCollection('sub_category');
        }
    }
}
