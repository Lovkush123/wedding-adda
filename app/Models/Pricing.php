<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    use HasFactory;

    protected $table = 'pricing'; // Table name

    protected $fillable = [
        'vendor_id', // Keeping vendor_id without foreign key
        'price_name',
        'price_type',
        'price_category',
        'price',
    ];
}
