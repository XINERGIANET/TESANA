<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientData extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'calculo',
        'date',
        'triceps',
        'subescapular',
        'suprailiaco',
        'abdominal',
        'muslo',
        'pantorrilla',
        'abdominal_2',
        'cadera',
        'peso',
        'talla',
    ];

    protected $dates = ['date'];

    public $timestamps = false;

    public function scopeActive(){
        return $this->whereHas('client', function($query){
            return $query->where('deleted', 0);
        });
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function calc_1(){
        $sex = optional($this->client)->sex;
        $sum = $this->triceps + $this->subescapular + $this->suprailiaco + $this->abdominal + $this->muslo + $this->pantorrilla;

        if($sex == 'M'){
            $total = 3.64 + ($sum * 0.097);
        }elseif($sex == 'F'){
            $total = 4.56 + ($sum * 0.143);
        }else{
            $total = 0;
        }
        
        return number_format($total, 4);
    }

    public function calc_2(){
        
        if($this->cadera > 0){
            $total = $this->abdominal_2 / $this->cadera;
        }else{
            $total = 0;
        }


        return number_format($total, 4);
    }

    public function calc_3(){
        if($this->talla > 0){
            $total = $this->peso / ($this->talla * $this->talla);
        }else{
            $total = 0;
        }

        return number_format($total, 4);
    }

    public function result_1(){
        $sex = optional($this->client)->sex;
        $age = optional($this->client)->age();
        $calc = $this->calc_1();

        $range = [
            'M' => [
                [15, 39, ['Bajo en grasa' => [0, 16], 'Saludable' => [16, 28], 'Sobrepeso' => [28, 39], 'Obesidad' => [39, 100]]],
                [40, 59, ['Bajo en grasa' => [0, 18], 'Saludable' => [18, 30], 'Sobrepeso' => [30, 40], 'Obesidad' => [40, 100]]],
                [60, 79, ['Bajo en grasa' => [0, 20], 'Saludable' => [20, 32], 'Sobrepeso' => [32, 42], 'Obesidad' => [42, 100]]],
            ],
            'F' => [
                [15, 39, ['Bajo en grasa' => [0, 8], 'Saludable' => [8, 20], 'Sobrepeso' => [20, 25], 'Obesidad' => [25, 100]]],
                [40, 59, ['Bajo en grasa' => [0, 11], 'Saludable' => [11, 22], 'Sobrepeso' => [22, 28], 'Obesidad' => [28, 100]]],
                [60, 79, ['Bajo en grasa' => [0, 13], 'Saludable' => [13, 25], 'Sobrepeso' => [25, 30], 'Obesidad' => [30, 100]]],
            ]
        ];

        foreach($range[$sex] as [$minAge, $maxAge, $rules]){
            if($age >= $minAge && $age <= $maxAge){
                foreach($rules as $category => [$min, $max]){
                    if($calc >= $min && $calc < $max){
                        return $category;
                    }
                }
            }
        }

        return 'Desconocido';
    }

    public function result_2(){
        $sex = optional($this->client)->sex;
        $calc = $this->calc_2();

        if($sex == 'M'){
            if($calc < 0.94){
                return 'Sin riesgo';
            }elseif($calc > 0.94){
                return 'Con riesgo';
            }

        }elseif($sex == 'F'){
            if($calc < 0.85){
                return 'Sin riesgo';
            }elseif($calc > 0.85){
                return 'Con riesgo';
            }
        }

        return 'Desconocido';
    }

    public function result_3(){
        $calc = $this->calc_3();

        if($calc < 18.5){
            return 'Por debajo';
        }elseif($calc >= 18.5 && $calc <= 24.9){
            return 'Saludable';
        }elseif($calc >= 25 && $calc <= 29.9){
            return 'Sobrepeso';
        }elseif($calc >= 30 && $calc <= 34.9){
            return 'Obesidad I';
        }elseif($calc >= 35 && $calc <= 39.9){
            return 'Obesidad II';
        }elseif($calc > 40){
            return 'Obesidad III';
        }else{
            return 'Desconocido';
        }

    }
    
}
