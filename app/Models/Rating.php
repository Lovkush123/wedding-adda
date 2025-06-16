<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    // Table name (optional if it follows Laravel's naming convention)
    protected $table = 'rating';

    // Primary key (since it's not 'id')
    protected $primaryKey = 'rating_id';

    // Fillable fields
    protected $fillable = [
        'user_id',
        'vender_id',
        'vender_type',
        'rating_value',
        'review',
    ];
}
