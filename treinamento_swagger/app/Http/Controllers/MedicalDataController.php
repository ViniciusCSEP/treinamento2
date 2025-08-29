<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;
use Illuminate\Http\Request;
use App\Models\MedicalData;
use Carbon\Carbon;

class MedicalDataController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Função que recebe, valida e armazena os valores informados no request par novos registros de informações de saúde
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            "presure_one" => "required",
            "presure_two" => "required",
            "glucose" => "required",
            "pain_level" => "required",
            "observation" => "required",
            "user_id" => "required",
        ]);

        $data = MedicalData::create([
            "presure_one" => $request->presure_one,
            "presure_two" => $request->presure_two,
            "glucose" => $request->glucose,
            "pain_level" => $request->pain_level,
            "obsevation" => $request->observation,
            "user_id" => $request->user_id
        ]);

        return response()->json(['data' => $data], 200);
    }

    /**
     * Função que busca valida os valores novos informados no request, busca pelo registro com o id informado e
     * atualiza com os valores novos.
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'presure_one' => 'required',
            'presure_two' => 'required',
            'glucose' => 'required',
            'pain_level' => 'required',
            'observation' => 'required',
            'id' => 'required'
        ]);

        $data = MedicalData::find($request->id);
        if(!$data){
            return response()->json(["message" => "Registro não encontrado"], 404);
        }
       $data->update([
                "presure_one" => $request->presure_one,
                "presure_two" => $request->presure_two,
                "glucose" => $request->glucose,
                "pain_level" => $request->pain_level,
                "obsevation" => $request->observation,
            ]);
            return response()->json(['data' => $data], 200);
    }

    /**
     * Função que busca pelo registro com o mesmo id que o informado e em caso de sucesso deleta ele do banco.
     */
    public function destroy($id)
    {
        $data = MedicalData::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['message' => "Registro excluído."], 200);
        }
        return response()->json(['message' => "Registro não encontrado"], 404);
    }

    /**
     * Função que busca pelo registro com o id informado e retorna os seus dados.
     */
    public function show($id)
    {
        $data = MedicalData::find($id);
        if ($data) {
            return response()->json(['data' => $data], 200);
        }
        return response()->json(['data' => "Registro não encontrado!"], 404);
    }

    /**
     * Função que busca pelos registros salvos com o id do usuário informado e os retorna.
     */

    public function index(Request $request)
    {
        $id = $request->input("id");
        $data = MedicalData::where('user_id', $id)->get();
        if ($data) {
            return response()->json(['data' => $data], 200);
        }
        return response()->json(['data' => 'Sem registros cadastrados nesse usuário'], 404);
    }

}
