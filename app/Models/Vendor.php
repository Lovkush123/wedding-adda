<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'sub_category_id', 'non_veg', 'veg',
        'starting_price', 'contact', 'mail', 'cover_image'
    ];

    // Auto-generate slug from name
    public static function boot()
    {
        parent::boot();
        static::creating(function ($vendor) {
            $vendor->slug = Str::slug($vendor->name);
        });
    }

    // Relationship with SubCategory
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
