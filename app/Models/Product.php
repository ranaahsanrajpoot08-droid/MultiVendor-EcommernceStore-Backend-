<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    protected $fillable = ['vendor_id', 'category_id', 'name', 'slug', 'description', 'price', 'discount_price', 'stock', 'image', 'is_active'];

    public function vendor() { return $this->belongsTo(Vendor::class); }
    public function category() { return $this->belongsTo(Category::class); }
}
