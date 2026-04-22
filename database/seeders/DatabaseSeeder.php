<?php
namespace Database\Seeders;

use App\Models\{User, Vendor, Category, Product};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        // Admin
        User::create(['name' => 'Admin', 'email' => 'admin@shop.com', 'password' => Hash::make('password'), 'role' => 'admin']);

        // Customer
        User::create(['name' => 'John Customer', 'email' => 'customer@shop.com', 'password' => Hash::make('password'), 'role' => 'customer']);

        // Vendor
        $vu = User::create(['name' => 'Vendor One', 'email' => 'vendor@shop.com', 'password' => Hash::make('password'), 'role' => 'vendor']);
        $vendor = Vendor::create(['user_id' => $vu->id, 'shop_name' => 'Tech Haven', 'description' => 'Best electronics', 'status' => 'approved']);

        // Categories
        $cats = [
            ['name' => 'Electronics', 'icon' => 'bi-laptop'],
            ['name' => 'Fashion', 'icon' => 'bi-bag'],
            ['name' => 'Home & Kitchen', 'icon' => 'bi-house'],
            ['name' => 'Books', 'icon' => 'bi-book'],
            ['name' => 'Sports', 'icon' => 'bi-bicycle'],
            ['name' => 'Beauty', 'icon' => 'bi-heart'],
        ];
        foreach ($cats as $c) {
            Category::create(['name' => $c['name'], 'slug' => Str::slug($c['name']), 'icon' => $c['icon']]);
        }

        // Sample Products
        $samples = [
            ['name' => 'Wireless Headphones', 'price' => 89.99, 'discount_price' => 69.99, 'category_id' => 1, 'image' => 'https://picsum.photos/seed/1/400'],
            ['name' => 'Smart Watch Pro', 'price' => 199.99, 'category_id' => 1, 'image' => 'https://picsum.photos/seed/2/400'],
            ['name' => 'Laptop 15"', 'price' => 899.00, 'category_id' => 1, 'image' => 'https://picsum.photos/seed/3/400'],
            ['name' => 'Leather Jacket', 'price' => 149.00, 'discount_price' => 119.00, 'category_id' => 2, 'image' => 'https://picsum.photos/seed/4/400'],
            ['name' => 'Running Shoes', 'price' => 79.99, 'category_id' => 5, 'image' => 'https://picsum.photos/seed/5/400'],
            ['name' => 'Coffee Maker', 'price' => 59.99, 'category_id' => 3, 'image' => 'https://picsum.photos/seed/6/400'],
            ['name' => 'Novel Best Seller', 'price' => 19.99, 'category_id' => 4, 'image' => 'https://picsum.photos/seed/7/400'],
            ['name' => 'Face Cream', 'price' => 29.99, 'category_id' => 6, 'image' => 'https://picsum.photos/seed/8/400'],
        ];

        foreach ($samples as $s) {
            Product::create([
                'vendor_id' => $vendor->id,
                'category_id' => $s['category_id'],
                'name' => $s['name'],
                'slug' => Str::slug($s['name']),
                'description' => 'High quality ' . $s['name'] . '. Best price guaranteed. Fast shipping available.',
                'price' => $s['price'],
                'discount_price' => $s['discount_price'] ?? null,
                'stock' => rand(10, 100),
                'image' => $s['image'],
            ]);
        }
    }
}
