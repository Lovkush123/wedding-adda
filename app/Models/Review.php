<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'stars',
        'review',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
