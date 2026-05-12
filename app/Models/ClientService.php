<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientService extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'service_id',
        'date',
        'start_date',
        'end_date',
        'payment_date',
        'total',
        'debt',
        'paid',
        'observation',
    ];

    protected $dates = ['date', 'start_date', 'end_date', 'payment_date'];

    public $timestamps = false;

    public function scopeActive(){
        return $this->whereHas('client', function($query){
            return $query->where('deleted', 0);
        });
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function service(){
        return $this->belongsTo(Service::class);
    }
}
