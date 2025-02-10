<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CidadeController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\ConsultaController;

Route::post('login', [AuthController::class, 'login']);

Route::get('/cidades', [CidadeController::class, 'index']);
Route::get('/medicos', [MedicoController::class, 'index']);


Route::middleware('auth:api')->group(function () {
    Route::get('user', [AuthController::class, 'user']);

    Route::get('cidades/{id_cidade}/medicos', [MedicoController::class, 'medicosDaCidade']);

    Route::post('medicos', [MedicoController::class, 'store']);
    Route::get('/medicos/{id_medico}/pacientes', [MedicoController::class, 'pacientesPorMedico']);

    Route::post('/pacientes', [PacienteController::class, 'store']);
    Route::post('/pacientes/{id_paciente}', [PacienteController::class, 'update']);

    Route::post('/medicos/consulta', [ConsultaController::class, 'store']);

});
