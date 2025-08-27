<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Consult;
use App\Models\Doctor;
use Carbon\Carbon;

class ConsultController extends Controller
{

    public function __construct()
    {
        //
    }

    /**
     * Função que valida e guarda os valores informados no request no tabela consults do banco.
     */
    public function store(Request $request){
        $this->validate($request, [
            "reason"=> "required",
            "hour"=> "required",
            "date"=> "required",
            "patient_id"=> "required",
            "doctor_id"=> "required",
        ]);

        $consult = Consult::create($request->all());

        return response()->json(['data' => $consult], 200);
    }

    /**
     * Função que verifica se o profissional selecionado possui consultas no dia selecionado,
     * em caso dele possuir, a função pega as horas desse profissional e retira da lista as 
     * horas que estão indisponíveis, caso não tenha horas indisponíveis, a função apenas retorna as horas do profissional.
     */
    public function showHours(Request $request){
        
        $consultDate = $request->input('consultDate');
        $doctorId = $request->input('doctorId');

        $horasIndisponiveis = Consult::where('doctor_id', $doctorId)
        ->where('date', $consultDate)
        ->pluck('hour')
        ->map(function ($hora) {
            return \Carbon\Carbon::parse($hora)->format('H:i');
        })
        ->toArray();

        $doctor = Doctor::where('id', $doctorId)->first();

        $inicio = $doctor->start_hour;
        $fim = $doctor->end_hour;
        $intervalo = $doctor->break_minutes;

        $horarios = [];
        $i = 0;

        while ($inicio < $fim) {

            $horaAtual = $inicio->format("H:i");
            if(!in_array($horaAtual, $horasIndisponiveis)){
                array_push($horarios, $horaAtual);
            }
            $inicio->addMinutes($intervalo);

            $i++;
        }

        return response()->json(["data"=> $horarios], 200);
    }

    /// OPCIONAL ///

    /**
     * Função que mostra os dados de uma consulta específica
     */
    public function show($id)
    {
        $consult = Consult::findOrFail($id);

        if($consult){
            return response()->json(['data' => $consult], 200);
        }

        return response()->json(['message' => 'Nenhuma consulta informada'], 404);
    }

    /**
     * Função que mostra todas as consultas do paciente com o id informado na query string.
     */
    public function index(Request $request)
    {
        $patient_id = $request->input('patient_id');

        $consults = Consult::where('patient_id', $patient_id)->get();

        if($consults){
            return response()->json(['data' => $consults], 200);
        }

        return response()->json(['message' => "Sem consultas nesse id"], 200);
    }

    /**
     * Função que deletada (desmarca) a consulta com o id informado na query string.
     */
    public function destroy(Request $request){
        $id = $request->input('id');

        $consult = Consult::findOrFail($id);

        if($consult){
            $consult->delete();
            return response()->json(['data' => $consult, 'message' => "consulta deletada"], 200);
        }
        return response()->json(['message' => 'consulta não encontrada.'], 404);
    }
}
