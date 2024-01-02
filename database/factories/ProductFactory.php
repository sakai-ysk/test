<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Company;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{

    protected $model = Product::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'img_path' => 'https://picsum.photos/200/300',  // 200x300のランダムな画像
            'product_name' => $this->faker->word,  // ダミーの商品名
            'price' => $this->faker->numberBetween(100, 10000),  // 100から10,000の範囲のダミー価格
            'stock' => $this->faker->randomDigit,  // 0から9のランダムな数字でダミーの在庫数
            'company_id' => Company::factory(),
            //
        ];
    }
}
