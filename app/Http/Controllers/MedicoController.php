<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medico;
use Illuminate\Support\Facades\Validator;
use App\Models\Consulta;
use Carbon\Carbon;

class MedicoController extends Controller
{

    
    public function index(Request $request)
    {
        $nome = $request->query('nome');
        $query = Medico::query();

        if ($nome) {
            $nome = preg_replace('/^(dr|dra)\s+/i', '', $nome);
            $query->where('nome', 'LIKE', "%$nome%");
        }

        $medicos = $query->select('id', 'nome', 'especialidade', 'cidade_id')
                        ->orderBy('nome', 'asc')
                        ->get();

        return response()->json($medicos);
    }

    public function medicosDaCidade($id_cidade, Request $request)
    {
        $nome = $request->query('nome');

        $query = Medico::where('cidade_id', $id_cidade);

        if ($nome) {
            $nome = preg_replace('/^(dr|dra)\s+/i', '', $nome);
            $query->where('nome', 'LIKE', "%$nome%");
        }

        $medicos = $query->orderBy('nome', 'asc')->get(['id', 'nome', 'especialidade', 'cidade_id']);

        return response()->json($medicos);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'especialidade' => 'required|string|max:255',
            'cidade_id' => 'required|exists:cidade,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $medico = Medico::create($request->only(['nome', 'especialidade', 'cidade_id']));

        return response()->json([
            'id' => $medico->id,
            'nome' => $medico->nome,
            'especialidade' => $medico->especialidade,
            'cidade_id' => $medico->cidade_id,
            'created_at' => $medico->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $medico->updated_at->format('Y-m-d H:i:s'),
            'deleted_at' => $medico->deleted_at ? $medico->deleted_at->format('Y-m-d H:i:s') : null,
        ], 201);
    }

    public function pacientesPorMedico($idMedico, Request $request)
    {
        $query = Consulta::with('paciente')
                        ->where('medico_id', $idMedico)

                        ->when($request->has('nome'), function ($query) use ($request) {
                            $query->whereHas('paciente', function ($query) use ($request) {
                                $query->where('nome', 'LIKE', '%' . $request->nome . '%');
                            });
                        })

                        ->when($request->query('apenas-agendadas'), function ($query) {
                            $query->where('data', '>', now());
                        });

        $consultas = $query->orderBy('data', 'asc')->get();

        $pacientes = $consultas->map(function ($consulta) {
            return [
                'id' => $consulta->paciente->id,
                'nome' => $consulta->paciente->nome,
                'cpf' => $consulta->paciente->cpf,
                'celular' => $consulta->paciente->celular,
                'created_at' => Carbon::parse($consulta->paciente->created_at)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($consulta->paciente->updated_at)->format('Y-m-d H:i:s'),
                'deleted_at' => $consulta->paciente->deleted_at ? Carbon::parse($consulta->paciente->deleted_at)->format('Y-m-d H:i:s') : null,
                'consulta' => [
                    'id' => $consulta->id,
                    'data' => Carbon::parse($consulta->data)->format('Y-m-d H:i:s'),
                    'created_at' => Carbon::parse($consulta->created_at)->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::parse($consulta->updated_at)->format('Y-m-d H:i:s'),
                    'deleted_at' => $consulta->deleted_at ? Carbon::parse($consulta->deleted_at)->format('Y-m-d H:i:s') : null,
                ],
            ];
        });

        return response()->json($pacientes);
    }


}
