<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Repositories\ClienteRepository;
use Illuminate\Http\Request;

class ClientesController extends Controller
{
    public function __construct(Clientes $clientes)
    {
        $this->clientes = $clientes;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $clientesRepository = new ClienteRepository($this->clientes);

        /*if($request->has('atributos_modelo')){
            $atributos_modelo = 'modelo:id,' . $request->atributos_modelo;
    
            $clientesRepository->selectAtributosRegistosRelacionados($atributos_modelo);
        }
        else {

           $clientesRepository->selectAtributosRegistosRelacionados('modelo');
        }*/
        if($request->has('filtro')){
            $clientesRepository->filtro($request->filtro);
        }
        if($request->has('atributos')){
            $clientesRepository->selectAtributos($request->atributos);
        }

        return response()->json($clientesRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate($this->clientes->regras(), $this->clientes->feedback());

        $clientes = $this->clientes->create([
            'nome' => $request->get('nome'),
        ]);

        return response()->json($clientes, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\clientes  $clientes
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
   
        $clientes = $this->clientes->find($id);
        if($clientes === null){
            return response()->json(["erro" => "Cliente não encontrado"], 404);
        }
        return response()->json($clientes, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\clientes  $marca
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, Marca $marca)
    public function update(Request $request, $id)
    {
 
        $clientes = $this->clientes->find($id);

        if($clientes === null){

            return response()->json(["erro" => "Impossível realizar a atualização! Cliente não encontrado!"], 404);
        }

        
        if($request->method() === 'PATCH'){
            $regrasDinamicas = [];

            foreach($clientes->regras() as $input => $regra){
         
                if(array_key_exists($input, $request->all())){
                    //$teste .= "Regra : " . $input . '<br>';
                    $regrasDinamicas[$input] = $regra;
                }

            }
            // return $teste;
            $request->validate($regrasDinamicas, $clientes->feedback());
        }else {
            $request->validate($clientes->regras(), $clientes->feedback()); 

        }

        $clientes->fill($request->all());
        //dd($clientes->getAttributes());
        $clientes->save();
        return response()->json($clientes, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\clientes  $clientes
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    
       $clientes = $this->clientes->find($id);
     
       if($clientes === null){
        return response()->json(["erro" => "Impossível eliminar cliente! Cliente não encontrado!"], 404);
       }
   
       $clientes->delete();
       return response()->json(["Cliente removido com sucesso"], 200);
    }
}
