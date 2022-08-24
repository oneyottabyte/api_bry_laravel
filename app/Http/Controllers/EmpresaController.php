<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use App\Repositories\EmpresaRepository;

class EmpresaController extends Controller
{
    public function __construct(Empresa $empresa) {
        $this->empresa = $empresa;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $empresaRepository = new EmpresaRepository($this->empresa);

        if($request->has('atributos_funcionarios')) {
            $atributos_funcionarios = 'funcionarios:id,'.$request->atributos_funcionarios;
            $empresaRepository->selectAtributosRegistrosRelacionados($atributos_funcionarios);
        } else {
            $empresaRepository->selectAtributosRegistrosRelacionados('funcionarios');
        }

        if($request->has('filtro')) {
            $empresaRepository->filtro($request->filtro);
        }

        if($request->has('atributos')) {
            $empresaRepository->selectAtributos($request->atributos);
        } 

        return response()->json($empresaRepository->getResultado(), 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEmpresaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->empresa->rules(), $this->empresa->feedback());

        $empresa = $this->empresa->create([
            'nome' => $request->nome,
            'cnpj' => $request->cnpj,
            'endereco' => $request->endereco,
        ]);

        return response()->json($empresa, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Empresa  $empresa
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $empresa = $this->empresa->with('funcionarios')->find($id);
        if($empresa === null) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404) ;
        } 
        return response()->json($empresa, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEmpresaRequest  $request
     * @param  \App\Models\Empresa  $empresa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $empresa = $this->empresa->find($id);

        if($empresa === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        if($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            //percorrendo todas as regras definidas no Model
            foreach($empresa->rules() as $input => $regra) {
                
                //coletar apenas as regras aplicáveis aos parâmetros parciais da requisição PATCH
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }
            
            $request->validate($regrasDinamicas, $empresa->feedback());

        } else {
            $request->validate($empresa->rules(), $empresa->feedback());
        }
        
        $empresa->fill($request->all());
        $empresa->save();
        
        return response()->json($empresa, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Empresa  $empresa
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $empresa = $this->empresa->find($id);

        if($empresa === null) {
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe'], 404);
        }
        
        $empresa->funcionarios()->detach();

        $empresa->delete();
        return response()->json(['msg' => 'A empresa foi removida com sucesso!'], 200);
    }

    public function addFuncionario(Request $request)
    {
        $empresa = $this->empresa->find($request->empresa_id);
        $empresa->funcionarios()->attach($request->funcionario_id);
        return response()->json($empresa, 200);
    }
}
