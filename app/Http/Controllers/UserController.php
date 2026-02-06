<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function criarUsuario()
    {
        User::create([
            'name' => 'Usuário Teste',
            'email' => 'teste@email.com',
            'password' => Hash::make('123456'),
        ]);

        return 'Usuário criado com sucesso!';
    }
}