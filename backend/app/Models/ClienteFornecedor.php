<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteFornecedor extends Model
{
    protected $table = 'clientes_fornecedores';

    protected $fillable = [
        'tipo',
        'nome_razao_social',
        'nome_fantasia',
        'cnpj_cpf',
        'ie_rg',
        'telefone_contato',
        'email_contato',
        'cep',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
    ];
}
