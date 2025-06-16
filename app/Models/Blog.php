<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    // Optional: specify the table name if it's not the plural of the model
    protected $table = 'blogs';

    // Mass assignable fields
    protected $fillable = [
        'title',
        'content',
        'slug',
        'author',
        'image',
        'status',
    ];

    // Optional: cast fields
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
