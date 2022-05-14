<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    use HasFactory;
   
    protected $fillable=['nome'];


    public function regras(){
        $id = $this->id ? $this->id : 'null';
        return [
            'nome' => 'required|unique:clientes,nome,' .$id . '|min:3',
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