<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasFactory;

    protected $table = 'enquiry'; // explicitly define since it's not plural

    protected $fillable = [
        'user_id',
        'vander_id',
        'enquiry_type',
        'note',
    ];
}
