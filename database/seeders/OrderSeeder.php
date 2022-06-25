<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\PaidProduct;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Order::truncate();
        PaidProduct::truncate();
        $users = User::get();
        foreach ($users as $key => $user) {
            for ($i = 0; $i < rand(2, 30); $i++) {

                $products = Product::get()->random(rand(3, 10));

                $products->transform(function ($item) {
                    $item->quantity_selected = rand(3, 10);
                    $item->price_quantity = $item->price * $item->quantity_selected;
                    return $item;
                });

                $amount = $products->sum('price_quantity');

                $charges = OrderService::calculate_total_price($amount);
                $status = ['successful', 'refunded', 'canceled'];

                $order = Order::create([
                    'code'=>OrderService::generate_code($user->id),
                    'quantity' => $products->count(),
                    'shipping' => $charges['shipping'],
                    'tax_amount' => $charges['tax_amount'],
                    'tax_percent' => $charges['tax_percent'],
                    'sub_total' => $charges['sub_total'],
                    'total' => $charges['total'],
                    'user_id' => $user->id,
                    'user_json' => $user->only(['name', 'email', 'phone']),
                    'status' => $status[rand(0, 2)],
                ]);

                $paid_products = $products->map(function ($item, $key) {

                    return [
                        'name' => $item->name,
                        'price' => $item->price,
                        'quantity' => $item->quantity_selected,
                        'price_quantity' => $item->price_quantity,
                        'product_id' => $item->id,
                    ];
                });
                $paid_products->toArray();

                $order->products()->createMany($paid_products);
            }
        }
    }
}
