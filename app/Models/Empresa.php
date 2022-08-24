<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'cnpj', 'endereco'];

    public function rules() {
        return [
            'nome' => 'required|min:3',
            'cnpj' => 'required|unique:empresas,cnpj,'.$this->id,
            'endereco' => 'required',
        ];

        /*
            1) tabela
            2) nome da coluna que será pesquisada na tabela3
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

    public function funcionarios()
    {
        return $this->belongsToMany('App\Models\Funcionario')->withTimestamps();;
    }
}
