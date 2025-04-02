<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_id', 'title', 'description'];

    /**
     * Get the vendor that owns the feature.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
