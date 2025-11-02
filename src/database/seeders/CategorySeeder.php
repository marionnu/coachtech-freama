<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'ファッション','家電','インテリア','レディース','メンズ','コスメ',
            '本','ゲーム','スポーツ','キッチン','ハンドメイド','アクセサリー',
            '食べもの','ベビー・キッズ','その他',
        ];

        foreach ($names as $n) {
            Category::firstOrCreate(['name' => $n], ['category_name' => $n]);
        }
    }
}
