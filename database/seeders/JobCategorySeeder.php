<?php

namespace Database\Seeders;

use App\Models\JobCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobCategorySeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            'Stavba','Úklid','Doprava','Montáž','Malování',
            'Zahrada','Pomoc v domácnosti','Sklad'
        ];
        foreach ($cats as $i => $name) {
            JobCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'sort' => ($i+1)*10]
            );
        }
    }

    // Удобный вызов из install-роута
    public static function runOnce(): void { (new static)->run(); }
}
