<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'product_id',
        'price',
        'quantity',
        'total',
        'payment_method_id',
        'date',
        'deleted'
    ];

    protected $dates = ['date'];

    public $timestamps = false;

    public function scopeActive($query){
        return $query->where('deleted', 0);
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function payment_method(){
        return $this->belongsTo(PaymentMethod::class);
    }
}
