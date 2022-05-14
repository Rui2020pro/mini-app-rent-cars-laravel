<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{
    public function __construct(Marca $marca)
    {
        // bk - desrtoy()
        $this->marca = $marca;
        //next - index()
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // back step MarcaController - store()
        // $marcas = Marca::all();
        /*return $marcas; */// retorno json - headers... ; next step show()
        // $marca = $this->marca->all(); // bk construct() ; next store()
        // $marca = $this->marca->with('modelos')->get(); // backstep index() ModeloController
        // return response()->json($marca, 200);// bck destroy() next store()

        $marcaRepository = new MarcaRepository($this->marca);

        if($request->has('atributos_modelos')){
            $atributos_modelos = 'modelos:id,' . $request->atributos_modelos;
            // $marcas = $this->marca->with('modelos:id,' . $atributos_modelos);
            $marcaRepository->selectAtributosRegistosRelacionados($atributos_modelos);
        }
        else {
           // $marcas = $this->marca->with('modelos'); // ->get() ;
           $marcaRepository->selectAtributosRegistosRelacionados('modelos');
        }
        if($request->has('filtro')){
            // dd($request->filtro); - http://localhost:8000/api/marca?filtro=nome:=:Ford KA 1.0
            // dd(explode(":", $request->filtro)) : nome - coluna, = - operador e Ford... - value
            
            // múltiplos filtros - http://localhost:8000/api/marca?filtro=nome:like:Ford%;numero_portas:>:3
            /*$filtros = explode(";", $request->filtro);
            foreach($filtros as $key => $condicao){
                $condicoes = explode(":", $condicao);
               $marcas = $marcas->where($condicoes[0], $condicoes[1], $condicoes[2]);
            }*/

            $marcaRepository->filtro($request->filtro);
        }
        if($request->has('atributos')){
            // dd($request->atributos);
            // $atributos = $request->atributos;
            // dd($atributos_marca);
            // $marcas = $marcas->selectRaw($atributos)->with('modelos')->get(); // nxt step app/Repositories/AbstractRepository.php
            // marca?atributos=id,imagem,nome,marca_id

            $marcaRepository->selectAtributos($request->atributos);

        }/*else{
           // $marcas = $marcas->get(); // só no final é que se executa o método get
        }
        return response()->json($marcas, 200);*/

        return response()->json($marcaRepository->getResultadoPaginado(3), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // back step api
        // return ['Chegámos até aqui'];
        // dd($request->all());
        // $marca = Marca::create($request->all());
        // next step Marca - $fillable
        // back step Marca - store()
        // return $marca;
        // next step MarcaController - index()

        // bk index() ; next show()
        /*$marca = $this->marca->create($request->all());
        return response()->json($marca, 201);*/ // bck index() next show()

        // bck destroy() next ()
        /*$regras = [
            'nome' => 'required|unique:marcas',
            'imagem' => 'required'
        ];
        $feedback = [
            'required' => 'O campo :attribute é obrigatório!',
            'nome.unique' => 'O nome da marca já existe'
        ];*/
        // retorno de marca vai voltar à raiz da aplicação caso não passemos um parâmetro
        // nos Headers teremos de meter accept -> application/json
        $request->validate($this->marca->regras(), $this->marca->feedback());

        // dd($request->file('imagem')); backctep update() 
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public'); // 1º Parâmetro - Path ; 2º Parâmetro - Disco config/filysysmtems.php; next step filysysmtems.php - default
        // dd("Upload de Arquivos"); // storage/app/imagens
        // dd($imagem_urn);
        $marca = $this->marca->create([
            'nome' => $request->get('nome'),
            'imagem' => $imagem_urn // next step Model Marca rules
        ]);

        //$marca = $this->marca->create($request->all());
        return response()->json($marca, 201);

        // next step implementar as regras e o feedback em Marca ao invés de MarcaController
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    // public function show(Marca $marca)
    public function show($id)
    {
        // back step index() - MarcaController
        // return $marca; // next step update()

        // bk store() ; next update()
        $marca = $this->marca->with('modelos')->find($id);
        // dd($marca) - return null caso não encontrar // bk destroy() next update()
        if($marca === null){
            return response()->json(["erro" => "Marca não encontrada"], 404); // bk destroy() next update()
        }
        return response()->json($marca, 200); // bck store() next update()
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, Marca $marca)
    public function update(Request $request, $id)
    {
        // back step show()

        /*print_r($request->all()); // dados atualizados
        echo '<hr>';
        print_r($marca->getAttributes());*/ // dados antigos

        /*$marca->update($request->all());
        return $marca;*/ // next step destroy()

        // bk show() ; next destroy()
        $marca = $this->marca->find($id);
        // dd($marca); // bk show() next destroy()
        if($marca === null){
            // bk show() next destroy()
            return response()->json(["erro" => "Impossível realizar a atualização! Marca não encontrada!"], 404);
        }
        //dd($request->method()); - put or patch
        
        if($request->method() === 'PATCH'){
            $regrasDinamicas = [];
            //$teste = '';

            //dd($marca->regras());
            // percorrer todas as regras definidas no model
            foreach($marca->regras() as $input => $regra){
                //$teste .= 'Input : ' . $input . ' Regra : ' .$regra . '<br>'; 

                // recolher apenas as regras aplicáveis aos parâmetros parciais da 
                // requisição PATCH
                if(array_key_exists($input, $request->all())){
                    //$teste .= "Regra : " . $input . '<br>';
                    $regrasDinamicas[$input] = $regra;
                }

            }
            // return $teste;
            $request->validate($regrasDinamicas, $marca->feedback());
        }else {
            $request->validate($marca->regras(), $marca->feedback()); 
            //backstep feedback Marca next uploadArquivos - store() 
        }

        $marca->fill($request->all());

        // remove a imagem antiga caso tenha passado uma nova imagem
        if($request->file('imagem')){
            Storage::disk('public')->delete($marca->imagem); // next step destroy()
            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens', 'public');
            $marca->imagem = $imagem_urn;
        }
        $marca->save();
        return response()->json($marca, 200);// bck show() next destroy()
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       // print_r($marca->getAttributes());
       /*$marca->delete();
       return ['msg' => "Marca Removida com Sucesso!"];*/
       // next step constructor

       // bk update() ; next show()
       $marca = $this->marca->find($id);
       // dd($marca); - bk update() next show()
       if($marca === null){
        // bk update() next index()
        return response()->json(["erro" => "Impossível eliminar marca! Marca não encontrada!"], 404);
       }
       Storage::disk('public')->delete($marca->imagem);// backstep update() - imagem next step Modelos()
       $marca->delete();
       return response()->json(["Marca removida com sucesso"], 200); // bck update() next store() 1
    }
}
