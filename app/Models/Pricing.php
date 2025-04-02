<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    use HasFactory;

    protected $table = 'pricing';

    protected $fillable = [
        'vender_id',
        'price_name',
        'price_type',
        'price_category',
        'price',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vender_id');
    }
}
