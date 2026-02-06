<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Rotas pÃºblicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
});

/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/

Route::post('/login', function (Request $request) {

    if (Auth::attempt($request->only('email', 'password'))) {
        return redirect('/dashboard');
    }

    return back()->with('error', 'Login invÃ¡lido');
});

/*
|--------------------------------------------------------------------------
| Dashboard (protegido)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
});

/*
|--------------------------------------------------------------------------
| Criar usuÃ¡rio (apenas para teste)
|--------------------------------------------------------------------------
*/



