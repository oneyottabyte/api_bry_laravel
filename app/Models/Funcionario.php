<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    use HasFactory;

    protected $fillable = ['login', 'nome', 'cpf','email','endereco','senha'];

    protected $hidden = ['senha' ];

    public function rules() {
        return [
            'login' => 'required|min:3',
            'nome' => 'required|min:3',
            'cpf' => 'required|unique:funcionarios,cpf,'.$this->id,
            'email' => 'required',
            'endereco' => 'required',
            'senha' => 'required'
        ];

        /*
            |unique|
            1) tabela
            2) nome da coluna que será pesquisada na tabela
            3) id do registro que será desconsiderado na pesquisa
        */
    }

    public function feedback() {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'min' => 'O campo :attribute deve ter no mínimo 3 caracteres',
            'unique' => 'O campo :attribute já existe'
        ];
    }

    public function empresas()
    {
        return $this->belongsToMany('App\Models\Empresa')->withTimestamps();;
    }

}
