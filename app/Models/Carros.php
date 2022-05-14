<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carros extends Model
{

    use HasFactory;
   
    protected $fillable=['modelo_id' , 'nome', 'placa', 'disponivel', 'km'];


    public function regras(){
        return [
            'modelo_id' => 'exists:modelos,id',
            'placa' => 'required',
            'disponivel' => 'required|boolean',
            'km' => 'required|integer',
        ];
    }
    public function feedback(){
        return [
            'required' => 'O campo :attribute é obrigatório!',
            'nome.unique' => 'O nome da marca já existe',
            'nome.min' => 'O nome da marca deve ter no mínimo 3 carateres',  
        ];
    }
    public function modelo(){
        // Um carro pertence a um modelo
        return $this->belongsTo('App\Models\Modelos'); // next step CarroRepository.php and CarrosController.php
    }
}
