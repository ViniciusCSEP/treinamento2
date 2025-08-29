<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Doctor;
use Carbon\Carbon;


class DoctorController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * Função que retorna uma lista com as especialidades dos profissionais cadastrados no banco.
     */
    public function especialities(){
        $especialities = Doctor::distinct()->pluck('especiality');

        return response()->json(['data' => $especialities]);
    }

    /**
     * Função que valida e guarda no banco os valores informados no request, assim guardando um novo profissional
     */

    public function store(Request $request)
    {
        $this->validate($request, [
            'especiality' => 'required|string|max:225',
            'start_hour' => 'required',
            'end_hour' => 'required',
            'break' => 'required',
            'user_id' => 'required',
            'week_days' => 'required|array|min:1'
        ]);

        $doctor = Doctor::create([
            'especiality' => $request->especiality,
            'start_hour' => $request->start_hour,
            'end_hour' => $request->end_hour,
            'break' => $request->break,
            'user_id' => $request->user_id,
            'week_days' => $request->week_days
        ]);

        return response()->json(['data' => $doctor, 'code' => 201]);
    }

    /**
     * Função que verifica se existe um profissional com o id informado no request, em caso de sucesso
     * retorna os dados desse profissional.
     */

    public function show(Request $request)
    {
        $id = $request->input('id');

        $doctor = Doctor::where('id', $id)->with('user')->first();

        if ($doctor) {
            return response()->json(['data' => $doctor, 'code' => 200]);
        }

        return response()->json(['data' => "Profissional não encontrado.", 'code' => 404]);
    }

    /**
     * Função que retorna as horas cadastradas pelo profissional na plataforma.
     */
    public function hours(Request $request)
    {
        $id = $request->input('id');

        $doctor = Doctor::where('id', $id)->first();

        $inicio = $doctor->start_hour;
        $fim = $doctor->end_hour;
        $intervalo = $doctor->break_minutes;

        $horarios = [];
        $i = 0;

        while ($inicio < $fim) {
            array_push($horarios, $inicio->format("H:i"));

            $inicio->addMinutes($intervalo);

            $i++;
        }

        return response()->json(['data' => $horarios, 'code' => 200]);
    }

    /**
     * Função que retorna uma lista dos profissionais cadastrados na plataforma, com a possibilidade deles serem filtrados ou
     * por especialidade, disponibilidade de data e disponibilidade de horário, caso esses sejam informados na query string.
     */
    public function doctors(Request $request)
    {
        $dias = [
            'dom',
            'seg',
            'ter',
            'qua',
            'qui',
            'sex',
            'sab'
        ];

        $time = $request->input('time');
        $especiality = $request->input('especiality');
        $date = $request->input('date');

        if ($time == 1) {
            // manhã
            $inicio = '05:00:00';
            $fim = '12:00:00';
        } else if ($time == 2) {
            //tarde
            $inicio = '12:00:00';
            $fim = '19:00:00';
        } else if ($time == 3) {
            //noite
            $inicio = '19:00:00';
            $fim = '23:59:00';
        }

        $doctors = Doctor::where("id", ">", 0);


        /**
         * Filtros por horário, especialidade e data
         */
        if ($time) {
            $doctors->where(function ($query) use ($inicio, $fim) {
                $query->whereBetween('start_hour', [$inicio, $fim])
                    ->orWhereTime('start_hour', "<=", $inicio);
            })->where(function ($query) use ($inicio, $fim) {
                $query->whereBetween('end_hour', [$inicio, $fim])
                    ->orWhereTime('end_hour', ">=", $fim);
            });
        }

        if ($especiality) {
            $doctors->where('especiality', $especiality);
        }

        if ($date) {
            $dia = Carbon::parse($date)->dayOfWeek;
            $doctors->whereJsonContains('week_days', $dias[$dia]);
        }

        return response()->json(['data' => $doctors->get()]);
    }

    //// OPCIONAL ////

    /**
     * Função que valida e atualiza os valores de um profissional no banco de dados.
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'especiality' => 'required|string|max:225',
            'start_hour' => 'required',
            'end_hour' => 'required',
            'break' => 'required',
            'sunday' => 'required|boolean',
            'monday' => 'required|boolean',
            'tuesday' => 'required|boolean',
            'wednesday' => 'required|boolean',
            'thursday' => 'required|boolean',
            'friday' => 'required|boolean',
            'saturday' => 'required|boolean',
            'week_days' => 'required|array|min:1',
            'doctor_id' => 'required'
        ]);

        $doctor = Doctor::where('id', $request->doctor_id)->first();

        $doctor->especiality = $request->especiality;
        $doctor->start_hour = $request->start_hour;
        $doctor->end_hour = $request->end_hour;
        $doctor->break = $request->break;
        $doctor->sunday = $request->sunday;
        $doctor->monday = $request->monday;
        $doctor->tuesday = $request->tuesday;
        $doctor->wednesday = $request->wednesday;
        $doctor->thursday = $request->thursday;
        $doctor->friday = $request->friday;
        $doctor->saturday = $request->saturday;
        $doctor->week_days = $request->week_days;

        $doctor->save();

        return response()->json(['data' => $doctor, 'code' => 201]);
    }

    /**
     * Função que localiza e em caso de sucesso deleta um profissional da plataforma
     */
    public function destroy(Request $request)
    {
        $id = $request->input('id');

        $doctor = Doctor::where('id', $id)->first();
        $user = User::where('id', $doctor->user_id)->first();

        if ($doctor && $user) {
            $doctor->delete();
            $user->delete();
            return response()->json(['data' => "Profissional deletado.", 'code' => 200]);
        }

        return response()->json(['data' => "Profissional não encontrado.", 'code' => 404]);
    }

}
