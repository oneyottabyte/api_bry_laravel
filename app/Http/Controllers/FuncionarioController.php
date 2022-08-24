<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use Illuminate\Http\Request;
use App\Repositories\FuncionarioRepository;

class FuncionarioController extends Controller
{
    public function __construct(Funcionario $funcionario) {
        $this->funcionario = $funcionario;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $funcionarioRepository = new FuncionarioRepository($this->funcionario);

        if($request->has('atributos_empresas')) {
            $atributos_empresas = 'empresas:id,'.$request->atributos_empresas;
            $funcionarioRepository->selectAtributosRegistrosRelacionados($atributos_empresas);
        } else {
            $funcionarioRepository->selectAtributosRegistrosRelacionados('empresas');
        }

        if($request->has('filtro')) {
            $funcionarioRepository->filtro($request->filtro);
        }

        if($request->has('atributos')) {
            $funcionarioRepository->selectAtributos($request->atributos);
        } 

        return response()->json($funcionarioRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreFuncionarioRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->funcionario->rules(), $this->funcionario->feedback());

        $funcionario = $this->funcionario->create([
            'login' => $request->login,
            'nome' => $request->nome,
            'cpf' => $request->cpf,
            'email' => $request->email,
            'endereco' => $request->endereco,
            'senha' => $request->senha
        ]);

        return response()->json($funcionario, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Funcionario  $funcionario
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $funcionario = $this->funcionario->with('empresas')->find($id);
        if($funcionario === null) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404) ;
        } 
        return response()->json($funcionario, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateFuncionarioRequest  $request
     * @param  \App\Models\Funcionario  $funcionario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $funcionario = $this->funcionario->find($id);

        if($funcionario === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        if($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            //percorrendo todas as regras definidas no Model
            foreach($funcionario->rules() as $input => $regra) {
                
                //coletar apenas as regras aplicáveis aos parâmetros parciais da requisição PATCH
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }
            
            $request->validate($regrasDinamicas, $funcionario->feedback());

        } else {
            $request->validate($funcionario->rules(), $funcionario->feedback());
        }
        
        $funcionario->fill($request->all());
        $funcionario->save();
        
        return response()->json($funcionario, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Funcionario  $funcionario
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $funcionario = $this->funcionario->find($id);

        if($funcionario === null) {
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe'], 404);
        }

        $funcionario->empresas()->detach();
        
        $funcionario->delete();
        return response()->json(['msg' => 'A funcionario foi removida com sucesso!'], 200);
    }

    public function addEmpresa(Request $request)
    {
        $funcionario = $this->funcionario->find($request->funcionario_id);
        $funcionario = $funcionario->empresas()->attach($request->empresa_id);
        return response()->json($funcionario, 200);
    }
}
