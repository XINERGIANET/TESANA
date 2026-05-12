<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sessions',
        'price',
        'deleted'
    ];

    public $timestamps = false;

    public function scopeActive($query){
        return $query->where('deleted', 0);
    }
}
