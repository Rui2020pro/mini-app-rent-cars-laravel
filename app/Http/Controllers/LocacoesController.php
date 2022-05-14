<?php

namespace App\Http\Controllers;

use App\Models\Locacoes;
use App\Repositories\LocacaoRepository;
use Illuminate\Http\Request;

class LocacoesController extends Controller
{
    public function __construct(Locacoes $locacoes)
    {
        $this->locacoes = $locacoes;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $locacoesRepository = new LocacaoRepository($this->locacoes);

        /*if($request->has('atributos_modelo')){
            $atributos_modelo = 'modelo:id,' . $request->atributos_modelo;
    
            $locacoesRepository->selectAtributosRegistosRelacionados($atributos_modelo);
        }
        else {

           $locacoesRepository->selectAtributosRegistosRelacionados('modelo');
        }*/
        if($request->has('filtro')){
            $locacoesRepository->filtro($request->filtro);
        }
        if($request->has('atributos')){
            $locacoesRepository->selectAtributos($request->atributos);
        }

        return response()->json($locacoesRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate($this->locacoes->regras(), $this->locacoes->feedback());

        $locacoes = $this->locacoes->create([
            'cliente_id' => $request->get('cliente_id'),
            'carro_id' => $request->get('carro_id'),
            'data_inicio_periodo' => $request->get('data_inicio_periodo'),
            'data_final_previsto_periodo' => $request->get('data_final_previsto_periodo'),
            'data_final_realizado_periodo' => $request->get('data_final_realizado_periodo'),
            'valor_diaria' => $request->get('valor_diaria'),
            'km_inicial' => $request->get('km_inicial'),
            'km_final' => $request->get('km_final'),
        ]);

        return response()->json($locacoes, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\locacoes  $locacoes
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
   
        $locacoes = $this->locacoes->find($id);
        if($locacoes === null){
            return response()->json(["erro" => "Locação não encontrada"], 404);
        }
        return response()->json($locacoes, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\locacoes  $marca
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, Marca $marca)
    public function update(Request $request, $id)
    {
 
        $locacoes = $this->locacoes->find($id);

        if($locacoes === null){

            return response()->json(["erro" => "Impossível realizar a atualização! Locação não encontrada!"], 404);
        }

        
        if($request->method() === 'PATCH'){
            $regrasDinamicas = [];

            foreach($locacoes->regras() as $input => $regra){
         
                if(array_key_exists($input, $request->all())){
                    //$teste .= "Regra : " . $input . '<br>';
                    $regrasDinamicas[$input] = $regra;
                }

            }
            // return $teste;
            $request->validate($regrasDinamicas, $locacoes->feedback());
        }else {
            $request->validate($locacoes->regras(), $locacoes->feedback()); 

        }

        $locacoes->fill($request->all());
        //dd($locacoes->getAttributes());
        $locacoes->save();
        return response()->json($locacoes, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\locacoes  $locacoes
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    
       $locacoes = $this->locacoes->find($id);
     
       if($locacoes === null){
        return response()->json(["erro" => "Impossível eliminar locação! Locação não encontrada!"], 404);
       }
   
       $locacoes->delete();
       return response()->json(["Locação removida com sucesso"], 200);

       // next step jwt-auth tymon
    }
}
