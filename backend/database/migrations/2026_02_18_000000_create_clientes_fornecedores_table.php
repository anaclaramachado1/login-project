<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clientes_fornecedores', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 30)->default('cliente_fornecedor');
            $table->string('nome_razao_social');
            $table->string('nome_fantasia', 255)->nullable();
            $table->string('cnpj_cpf', 20)->nullable();
            $table->string('ie_rg', 20)->nullable();
            $table->string('telefone_contato', 20)->nullable();
            $table->string('email_contato', 255)->nullable();
            $table->string('cep', 9)->nullable();
            $table->string('endereco')->nullable();
            $table->string('numero', 30)->nullable();
            $table->string('complemento', 100)->nullable();
            $table->string('bairro', 100)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->string('uf', 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes_fornecedores');
    }
};
