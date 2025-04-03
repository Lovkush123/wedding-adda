<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'category_id', 'subcategory_id', 'address1', 'address2', 'map_url', 'state', 'city', 'country', 
        'based_area', 'short_description', 'about_title', 'text_editor', 
        'call_number', 'whatsapp_number', 'mail_id', 'cover_image'
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
