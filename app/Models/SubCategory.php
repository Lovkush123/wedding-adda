<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'slug', 'image', 'description'];

    // Automatically generate a slug when creating a new SubCategory
    public static function boot()
    {
        parent::boot();

        static::creating(function ($subcategory) {
            $subcategory->slug = Str::slug($subcategory->name);
        });
    }

     /**
     * Get the category that owns the subcategory.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
}
