<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository{
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    public function selectAtributosRegistosRelacionados($atributos){
        $this->model = $this->model->with($atributos);
    }
    public function filtro($filtros){
        $filtros = explode(";", $filtros);
        foreach($filtros as $key => $condicao){
            $condicoes = explode(":", $condicao);
           $this->model = $this->model->where($condicoes[0], $condicoes[1], $condicoes[2]);
        }
    }
    public function selectAtributos($atributos){
        $this->model = $this->model->selectRaw($atributos);
    }
    public function getResultado(){
        return $this->model->get();
    }
    public function getResultadoPaginado($registosPorPagina){
        return $this->model->paginate($registosPorPagina);
    }
}

?>