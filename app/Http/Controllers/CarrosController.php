<?php

namespace App\Http\Controllers;

use App\Models\Carros;
use App\Repositories\CarroRepository;
use Illuminate\Http\Request;

class CarrosController extends Controller
{
    public function __construct(Carros $carro)
    {
        $this->carro = $carro;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $carroRepository = new CarroRepository($this->carro);

        if($request->has('atributos_modelo')){
            $atributos_modelo = 'modelo:id,' . $request->atributos_modelo;
    
            $carroRepository->selectAtributosRegistosRelacionados($atributos_modelo);
        }
        else {

           $carroRepository->selectAtributosRegistosRelacionados('modelo');
        }
        if($request->has('filtro')){
            $carroRepository->filtro($request->filtro);
        }
        if($request->has('atributos')){
            $carroRepository->selectAtributos($request->atributos);
        }

        return response()->json($carroRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate($this->carro->regras(), $this->carro->feedback());

        $carro = $this->carro->create([
            'modelo_id' => $request->get('modelo_id'),
            'placa' => $request->get('placa'),
            'disponivel' => $request->get('disponivel'),
            'km' => $request->get('km'),
        ]);

        return response()->json($carro, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
   
        $carro = $this->carro->with('modelo')->find($id);
        if($carro === null){
            return response()->json(["erro" => "Carro não encontrado"], 404);
        }
        return response()->json($carro, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\carro  $marca
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, Marca $marca)
    public function update(Request $request, $id)
    {
 
        $carro = $this->carro->find($id);

        if($carro === null){

            return response()->json(["erro" => "Impossível realizar a atualização! Carro não encontrado!"], 404);
        }

        
        if($request->method() === 'PATCH'){
            $regrasDinamicas = [];

            foreach($carro->regras() as $input => $regra){
         
                if(array_key_exists($input, $request->all())){
                    //$teste .= "Regra : " . $input . '<br>';
                    $regrasDinamicas[$input] = $regra;
                }

            }
            // return $teste;
            $request->validate($regrasDinamicas, $carro->feedback());
        }else {
            $request->validate($carro->regras(), $carro->feedback()); 

        }

        $carro->fill($request->all());
        //dd($carro->getAttributes());
        $carro->save();
        return response()->json($carro, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    
       $carro = $this->carro->find($id);
     
       if($carro === null){
        return response()->json(["erro" => "Impossível eliminar carro! Carro não encontrado!"], 404);
       }
   
       $carro->delete();
       return response()->json(["Carro removido com sucesso"], 200);
    }
}
