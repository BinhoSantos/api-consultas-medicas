<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cidade;
class CidadeController extends Controller
{
    public function index(Request $request)
    {
        $nome = $request->query('nome');

        $query = Cidade::query();

        if ($nome) {
            $query->where('nome', 'LIKE', "%$nome%");
        }

        $cidades = $query->select('id', 'nome', 'estado')
                        ->orderBy('nome', 'asc')
                        ->get();

        return response()->json($cidades);
    }

}
