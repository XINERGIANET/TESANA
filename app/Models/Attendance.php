<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'date',
        'active'
    ];

    protected $dates = ['date'];
    
    public $timestamps = false;

    public function client(){
        return $this->belongsTo(Client::class);
    }
}
