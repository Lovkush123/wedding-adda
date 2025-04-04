<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\SubCategory;
use App\Models\Vendor;
class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'image', 'description', 'service_id'];

    // Automatically set slug from name
    public static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }


    public function subcategories()
    {
        return $this->hasMany(SubCategory::class); // ✅ Use correct class name
    }
    public function vendors()
{
    return $this->hasMany(Vendor::class, 'category_id');
}
    
}
