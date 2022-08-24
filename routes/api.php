<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('funcionario', 'App\Http\Controllers\FuncionarioController');
Route::post('funcionario/empresa', 'App\Http\Controllers\FuncionarioController@addEmpresa');

Route::apiResource('empresa', 'App\Http\Controllers\EmpresaController');
Route::post('empresa/funcionario', 'App\Http\Controllers\EmpresaController@addFuncionario');