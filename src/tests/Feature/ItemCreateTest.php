<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemCreateTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        Category::factory()->create(['name'=>'ファッション']);
    }

    public function test_guest_cannot_access_create(): void
    {
        $this->get(route('items.create'))->assertRedirect(route('login'));
    }

    public function test_user_can_post_item(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $cat = Category::first();

        $res = $this->post(route('items.store'), [
            'name'       => 'テスト商品',
            'price'      => 1200,
            'condition'  => 1,
            'categories' => [$cat->id],
        ]);

        $res->assertRedirect(route('items.index'));
        $this->assertDatabaseHas('items', ['name'=>'テスト商品']);
    }

    public function test_other_user_cannot_edit_item(): void
    {
        $owner = User::factory()->create();
        $item  = Item::factory()->create(['user_id'=>$owner->id]);
        $other = User::factory()->create();

        $this->actingAs($other);
        $this->get(route('items.edit',$item))->assertForbidden();
    }
}
