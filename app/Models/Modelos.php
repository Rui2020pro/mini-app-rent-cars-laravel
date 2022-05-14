<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelos extends Model
{
    use HasFactory;
    // backstep destroy() - MarcaController next step index,show,update and destroy - ModelosController
    protected $fillable=['marca_id' , 'nome', 'imagem', 'numero_portas', 'lugares', 'air_bag' , 'abs'];


    public function regras(){
        $id = $this->id ? $this->id : 'null';
        return [
            'marca_id' => 'exists:marcas,id',
            'nome' => 'required|unique:marcas,nome,' .$id . '|min:3',
            'imagem' => 'required|file|mimes:png,jpeg',
            'numero_portas' => 'required|integer|digits_between:1,5',
            'lugares' => 'required|integer|digits_between:1,20',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean'
        ];
    }
    public function feedback(){
        return [
            'required' => 'O campo :attribute é obrigatório!',
            'nome.unique' => 'O nome da marca já existe',
            'nome.min' => 'O nome da marca deve ter no mínimo 3 carateres',
            'imagem.mimes' => 'A imagem deve ser do tipo png ou jpeg'        
        ];
    }
    public function marca(){
        // Um modelo pertence a uma marca
        return $this->belongsTo('App\Models\Marca'); // next step Marca modelos()
    }


}
