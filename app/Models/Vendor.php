<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($vendor) {
            $vendor->slug = Str::slug($vendor->name);
        });
    }

    // Relationship with Category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Relationship with SubCategory
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    // Relationship with Images
    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'vendor_id');
    }

    // ✅ New: Relationship with Features
    public function features(): HasMany
    {
        return $this->hasMany(Feature::class, 'vendor_id');
    }
    public function pricing(): HasMany
{
    return $this->hasMany(Pricing::class, 'vendor_id');
}
}
