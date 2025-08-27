<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\BrazilState;
use Carbon\Carbon;

class BrazilStateController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * Função que retorna uma lista com todos os estados cadastrados no banco de dados.
     */
    public function show() {
        $states = BrazilState::all();
        if($states){
            return response()->json(['data' => $states], 200);
        }
        
        return response()->json(['message' => 'Erro, sem dados no sistema.'], 404);
    }
}
