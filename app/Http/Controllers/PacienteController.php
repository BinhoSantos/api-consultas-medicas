<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paciente;
use Illuminate\Support\Facades\Validator;

class PacienteController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:20|unique:paciente,cpf',
            'celular' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $paciente = Paciente::create($request->only(['nome', 'cpf', 'celular']));

        return response()->json([
            'id' => $paciente->id,
            'nome' => $paciente->nome,
            'cpf' => $paciente->cpf,
            'celular' => $paciente->celular,
            'created_at' => $paciente->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $paciente->updated_at->format('Y-m-d H:i:s'),
            'deleted_at' => $paciente->deleted_at ? $paciente->deleted_at->format('Y-m-d H:i:s') : null,
        ], 201);
    }


    public function update(Request $request, $idPaciente)
    {

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'celular' => 'required|string|max:20',
        ]);

        $paciente = Paciente::find($idPaciente);

        if (!$paciente) {
            return response()->json(['error' => 'Paciente não encontrado'], 404);
        }

        $paciente->nome = $validated['nome'];
        $paciente->celular = $validated['celular'];

        $paciente->save();

        return response()->json([
            'id' => $paciente->id,
            'nome' => $paciente->nome,
            'cpf' => $paciente->cpf,
            'celular' => $paciente->celular,
            'created_at' => $paciente->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $paciente->updated_at->format('Y-m-d H:i:s'),
            'deleted_at' => $paciente->deleted_at ? $paciente->deleted_at->format('Y-m-d H:i:s') : null,
        ], 200);
    }
}
