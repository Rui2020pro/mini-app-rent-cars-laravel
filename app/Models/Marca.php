<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    // backstep store() - MarcaController
    // next step store() - MarcaController
    protected $fillable=['nome', 'imagem'];


    public function regras(){
        $id = $this->id ? $this->id : 'null';
        return [
            'nome' => 'required|unique:marcas,nome,' .$id . '|min:3',
            'imagem' => 'required|file|mimes:png,jpeg' // backstep store() - uploadArquivos
        ];
    }
    public function feedback(){
        return [
            'required' => 'O campo :attribute é obrigatório!',
            'nome.unique' => 'O nome da marca já existe',
            'nome.min' => 'O nome da marca deve ter no mínimo 3 carateres',
            'imagem.mimes' => 'A imagem deve ser do tipo png ou jpeg' // next step php artisan storage:link
            // http://localhost:8000/storage/imagens/pHmNFK9NEgsalMjVwtSw0hPCkbUxd7QZvPdRg3Ga.png // next step update()
        ];
    } // back step store() - MarcaController next step update() - MarcaController

    public function modelos(){
        // Uma Marca tem n modelos
        return $this->hasMany('App\Models\Modelos'); // next step show() - ModeloController
    }




}
