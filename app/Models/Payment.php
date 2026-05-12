<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_service_id',
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

    public function client_service(){
        return $this->belongsTo(ClientService::class);
    }

    public function payment_method(){
        return $this->belongsTo(PaymentMethod::class);
    }

    
    
}
