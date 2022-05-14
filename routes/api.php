<?php

use Illuminate\Http\Request;
use Illuminate\Routing\RouteGroup;
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
    return $request->user(); // [] - application/json
});

// http://localhost:8000/api/v1/clientes
// bck kernel.php
// nxt step postman, headers: authorization -> Bearer $token
Route::prefix('v1')->middleware('jwt-auth')->group(function (){
    Route::apiResource('clientes', 'App\Http\Controllers\ClientesController');
    Route::apiResource('carros', 'App\Http\Controllers\CarrosController');
    Route::apiResource('locacoes', 'App\Http\Controllers\LocacoesController');
    Route::apiResource('marcas', 'App\Http\Controllers\MarcaController');
    Route::apiResource('modelos', 'App\Http\Controllers\ModelosController');

    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
    Route::post('me', 'App\Http\Controllers\AuthController@me'); 
});

// next step store() - MarcaController

Route::post('login', 'App\Http\Controllers\AuthController@login');
Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
// next php artisan make:controller AuthController
