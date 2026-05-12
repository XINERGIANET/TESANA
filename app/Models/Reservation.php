<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'date',
        'reservation_date',
        'reservation_time',
    ];

    protected $dates = ['date', 'reservation_date', 'reservation_time'];
    
    public $timestamps = false;

    public function client(){
        return $this->belongsTo(Client::class);
    }
}
