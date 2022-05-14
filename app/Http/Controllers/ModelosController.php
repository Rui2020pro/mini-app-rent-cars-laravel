<?php

namespace App\Http\Controllers;

use App\Models\Modelos;
use App\Repositories\ModeloRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModelosController extends Controller
{
    public function __construct(Modelos $modelo)
    {
        $this->modelo = $modelo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    public function index(Request $request)
    {
        // modelos = array();
        // dd($request->atributos); // modelos?atributos=id,imagem,nome

        $modelosRepository = new ModeloRepository($this->modelo);

        if($request->has('atributos_marca')){
            $atributos_marca = 'marca:id' . $request->atributos_marca;
            /*$modelos = $this->modelo->with('marca:id,' . $atributos_marca);*/
            $modelosRepository->selectAtributosRegistosRelacionados($atributos_marca);

        }
        else {
            // $modelos = $this->modelo->with('marca'); // ->get();
            $modelosRepository->selectAtributosRegistosRelacionados('marca');
        }
        if($request->has('filtro')){
            // dd($request->filtro); - http://localhost:8000/api/modelos?filtro=nome:=:Ford KA 1.0
            // dd(explode(":", $request->filtro)) : nome - coluna, = - operador e Ford... - value
            
            // múltiplos filtros - http://localhost:8000/api/modelos?filtro=nome:like:Ford%;numero_portas:>:3
            /*$filtros = explode(";", $request->filtro);
            foreach($filtros as $key => $condicao){
                $condicoes = explode(":", $condicao);
                $modelos = $modelos->where($condicoes[0], $condicoes[1], $condicoes[2]);
            }*/

            $modelosRepository->filtro($request->filtro);
        }
        if($request->has('atributos')){
            // dd($request->atributos);
            // $atributos = $request->atributos;
            // dd($atributos_marca);
            // $modelos = $modelos->selectRaw($atributos)->with('marca')->get();
            // modelos?atributos=id,imagem,nome,marca_id

            $modelosRepository->selectAtributos($request->atributos);

        }/* else{
            $modelos = $modelos->get(); // só no final é que se executa o método get
        }

        //$modelo = $this->modelo->all(); 
        //$modelo = $this->modelo->with('marca')->get(); // next step index() - MarcaController
        return response()->json($modelos, 200); */

        // http://localhost:8000/api/marcas?atributos_modelos=id,nome,marca_id&filtro=nome:like:For%
        return response()->json($modelosRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->regras(), $this->modelo->feedback());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');
        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->get('nome'),
            'imagem' => $imagem_urn,
            'lugares' => $request->get('lugares'),
            'numero_portas' => $request->get('numero_portas'),
            'air_bag' => $request->get('air_bag'),
            'abs' => $request->get('abs')
        ]);
        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelos  $modelos
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $modelo = $this->modelo->with('marca')->find($id);

        if($modelo === null){
            return response()->json(["erro" => "Modelo não encontrado"], 404);
        }
        return response()->json($modelo, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelos  $modelos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $modelo = $this->modelo->find($id);
        if($modelo === null){
            return response()->json(["erro" => "Impossível realizar a atualização! Modelo não encontrado!"], 404);
        }
        if($request->method() === 'PATCH'){
            $regrasDinamicas = [];

            foreach($modelo->regras() as $input => $regra){
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }

            }
            $request->validate($regrasDinamicas, $modelo->feedback());
        }else {
            $request->validate($modelo->regras(), $modelo->feedback()); 
        }
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        if($request->file('imagem')){
            Storage::disk('public')->delete($modelo->imagem); 
        }

        $modelo->fill($request->all());
        $modelo->imagem = $imagem_urn;
        $modelo->save(); // next method index(Request $request)

        /*$modelo->update([ 
            'marca_id' => $request->marca_id,
            'nome' => $request->get('nome'),
            'imagem' => $imagem_urn,
            'lugares' => $request->get('lugares'),
            'numero_portas' => $request->get('numero_portas'),
            'air_bag' => $request->get('air_bag'),
            'abs' => $request->get('abs')
        ]);*/
        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelos  $modelos
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $modelo = $this->modelo->find($id); // next step show() MarcaController
        if($modelo === null){
            return response()->json(["erro" => "Impossível eliminar modelo! Modelo não encontrado!"], 404);
        }
        Storage::disk('public')->delete($modelo->imagem);
        $modelo->delete();
        return response()->json(["Modelo removido com sucesso"], 200);
    } // next step Model Modelos marca()
}
