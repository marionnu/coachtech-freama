<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Category;
use Illuminate\Support\Arr;

class TestItemsSeeder extends Seeder
{
    public function run(): void
    {
        // 出品者（無ければ作る）
        $user = User::first() ?? User::factory()->create([
            'name' => 'seed_user',
            'email' => 'seed@example.com',
            // 既にFactoryがあればそれを優先。直指定するならハッシュしておく
            // 'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // ★ カテゴリが無ければ1件作成（「その他」）
        $category = Category::first() ?? Category::create([
            'category_name' => 'ファッション','家電','インテリア','レディース','メンズ','コスメ',
            '本','ゲーム','スポーツ','キッチン','ハンドメイド','アクセサリー',
            '食べもの','ベビー・キッズ','その他',
        ]);
        $categoryMap = []; // name => id
        foreach ($categoryNames as $name) {
            $cat = Category::firstOrCreate(['name' => $name]);
            $categoryMap[$name] = $cat->id;
        }

        // 文字のコンディション -> 数値にマップ（あなたのItemアクセサと揃える）
        $condMap = [
            '良好' => 1,
            '目立った傷や汚れなし' => 2,
            'やや傷や汚れあり' => 3,
            '状態が悪い' => 4,
        ];

        // スクショのデータ（必要に応じてカテゴリIDは固定で1に）
        $rows = [
            ['item_name'=>'腕時計','price'=>15000,'brand_name'=>'Rolax','description'=>'スタイリッシュなデザインのメンズ腕時計','img'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg','condition'=>'良好'],
            ['item_name'=>'HDD','price'=>5000,'brand_name'=>'西芝','description'=>'高速で信頼性の高いハードディスク','img'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg','condition'=>'目立った傷や汚れなし'],
            ['item_name'=>'玉ねぎ3束','price'=>300,'brand_name'=>null,'description'=>'新鮮な玉ねぎ3束のセット','img'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg','condition'=>'やや傷や汚れあり'],
            ['item_name'=>'革靴','price'=>4000,'brand_name'=>null,'description'=>'クラシックなデザインの革靴','img'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg','condition'=>'状態が悪い'],
            ['item_name'=>'ノートPC','price'=>45000,'brand_name'=>null,'description'=>'高性能なノートパソコン','img'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg','condition'=>'良好'],
            ['item_name'=>'マイク','price'=>8000,'brand_name'=>null,'description'=>'高音質のレコーディング用マイク','img'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg','condition'=>'目立った傷や汚れなし'],
            ['item_name'=>'ショルダーバッグ','price'=>3500,'brand_name'=>null,'description'=>'おしゃれなショルダーバッグ','img'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg','condition'=>'やや傷や汚れあり'],
            ['item_name'=>'タンブラー','price'=>500,'brand_name'=>null,'description'=>'使いやすいタンブラー','img'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg','condition'=>'状態が悪い'],
            ['item_name'=>'コーヒーミル','price'=>4000,'brand_name'=>'Starbacks','description'=>'手動のコーヒーミル','img'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg','condition'=>'良好'],
            ['item_name'=>'メイクセット','price'=>2500,'brand_name'=>null,'description'=>'便利なメイクアップセット','img'=>'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg','condition'=>'目立った傷や汚れなし'],
        ];

        // ▼ 既存データを一旦クリア（開発用）
        ItemImage::query()->delete();
        // ピボットを先に消す
        \DB::table('category_item')->truncate();
        Item::query()->delete();

        foreach ($rows as $r) {
            $item = Item::create([
                'user_id'     => $user->id,
                'name'        => $r['name'],
                'brand_name'  => $r['brand_name'],
                'price'       => $r['price'],
                'description' => $r['description'],
                'condition'   => Arr::get($condMap, $r['condition'], 3),
                'status'      => 1, // 公開
            ]);

            // 画像1枚（URL保存）＋ sort_order
            $item->images()->create([
                'path'       => $r['img'],
                'sort_order' => 1,
            ]);

            // 多対多でカテゴリを付与
            $attachIds = collect($r['categories'] ?? ['その他'])
                ->map(fn($name) => $categoryMap[$name] ?? null)
                ->filter()->all();

            $item->categories()->sync($attachIds);
        }
    }
}