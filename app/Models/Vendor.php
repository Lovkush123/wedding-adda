<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'category_id', 'subcategory_id', 'address', 'state', 'city', 'country', 
        'veg_price', 'non_veg_price', 'price_type', 'starting_price', 'ending_price', // Added starting_price and ending_price
        'about_title', 'text_editor', 'call_number', 'whatsapp_number', 'mail_id', 'room_price', 'cover_image'
    ];

    // Auto-generate slug from name
    public static function boot()
    {
        parent::boot();
        static::creating(function ($vendor) {
            $vendor->slug = Str::slug($vendor->name);
        });
    }

    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship with SubCategory
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
