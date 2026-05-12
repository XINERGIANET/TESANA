<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'document',
        'name',
        'email',
        'phone',
        'deleted'
    ];

    public $timestamps = false;

    public function scopeActive($query){
        return $query->where('deleted', 0);
    }
    
}
