<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consulta;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class ConsultaController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'medico_id' => 'required|exists:medico,id',
            'paciente_id' => 'required|exists:paciente,id',
            'data' => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $consulta = Consulta::create($request->only(['medico_id', 'paciente_id', 'data']));

        return response()->json([
            'id' => $consulta->id,
            'medico_id' => $consulta->medico_id,
            'paciente_id' => $consulta->paciente_id,
            'data' => Carbon::parse($consulta->data)->format('Y-m-d H:i:s'),
            'created_at' => $consulta->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $consulta->updated_at->format('Y-m-d H:i:s'),
            'deleted_at' => $consulta->deleted_at ? $consulta->deleted_at->format('Y-m-d H:i:s') : null,
        ], 201);
    }

}
