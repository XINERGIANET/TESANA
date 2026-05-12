<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'document',
        'name',
        'birth_date',
        'sex',
        'email',
        'phone',
        'emergency_phone',
        'service_id',
        'date',
        'start_date',
        'end_date',
        'payment_date',
        'total',
        'sessions',
        'services',
        'password',
        'deleted'
    ];

    protected $dates = ['birth_date', 'date', 'start_date', 'end_date','payment_date'];

    public $timestamps = false;

    public function scopeActive($query){
        return $query->where('deleted', 0);
    }

    public function age(){
        if($this->birth_date){
            return $this->birth_date->age;
        }

        return null;
    }

    public function profile(){
        if(strtotime($this->end_date) < strtotime(date('Y-m-d'))){
            return 'Inactivo';
        }

        if($this->services == 1){
            return 'Nuevo';
        }elseif($this->services > 1 ){
            return 'Recurrente';
        }
    }

    public function service(){
        return $this->belongsTo(Service::class);
    }

    public function services(){
        return $this->hasMany(ClientService::class);
    }

    public function data(){
        return $this->hasMany(ClientData::class);
    }
}
