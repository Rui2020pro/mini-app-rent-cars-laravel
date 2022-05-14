<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locacoes extends Model
{
    use HasFactory;

    protected $table = 'locacoes';
   
    protected $fillable=[
        'cliente_id', 
        'carro_id', 
        'data_inicio_periodo', 
        'data_final_previsto_periodo',
        'data_final_realizado_periodo',
        'valor_diaria',
        'km_inicial',
        'km_final'
    ];


    public function regras(){
        return [
            'cliente_id' => 'exists:clientes,id|required',
            'carro_id' => 'exists:carros,id|required',
            'data_inicio_periodo' => 'required|date',
            'data_final_previsto_periodo' => 'required|date',
            'data_final_realizado_periodo' => 'required|date',
            'valor_diaria' => 'required|numeric',
            'km_inicial' => 'required|integer',
            'km_final' => 'required|integer',
            
        ];
    }
    public function feedback(){
        return [
            'required' => 'O campo :attribute é obrigatório!', 
        ];
    }
    /*public function modelo(){
        // Um carro pertence a um modelo
        return $this->belongsTo('App\Models\Modelos'); // next step CarroRepository.php and CarrosController.php
    }*/
}
