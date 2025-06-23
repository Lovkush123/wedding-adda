<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityVendor extends Model
{
    use HasFactory;
     protected $fillable = [
        'vendor_id',
        'community_id',
    ];

  
    public function cast()
    {
        return $this->hasMany(Community::class, 'community_id')->where('type', 'cast');
    }

    public function event()
    {
        return $this->hasMany(Community::class, 'community_id')->where('type', 'event');
    }
   public function vendors()
{
    return $this->belongsToMany(Vendor::class, 'community_vendor', 'community_id', 'vendor_id');
}
}
