<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'amount',
        'payment_method_id',
        'date',
        'deleted'
    ];

    protected $dates = ['date'];

    public $timestamps = false;

    public function scopeActive($query){
        return $query->where('deleted', 0);
    }
    
    public function payment_method(){
        return $this->belongsTo(PaymentMethod::class);
    }
}
