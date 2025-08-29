<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
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
     * Função que valida os valores informados no request e em caso de sucesso cadastra um usuário na plataforma.
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            "name" => "required|string",
            "email" => "required|email",
            "password" => "required|string|min:8",
            "type" => "required",
            "cpf" => "required",
            'phone' => "required",
            'city' => "required|string",
            'neighborhood' => "required",
            'street' => "required",
            'complement' => "required",
            'state_id' => "required"
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "type" => $request->type,
            "cpf" => $request->cpf,
            "phone" => $request->phone,
            "city" => $request->city,
            "state_id" => $request->state_id,
            "neigborhood" => $request->neighborhood,
            "street" => $request->street,
            "complement" => $request->complement
        ]);

        return response()->json(['data' => $user], 200);
    }

    /**
     * Função que valida o email e a senha com as do usuário cadastrado no banco, em caso de sucesso, gera um token, atribui ele
     * ao usuário logado e o retorna via json.
     */
    public function login(Request $request){
        $this->validate($request, [
            'email'=> 'required|email',
            'password'=> 'required'
            ]);

            $user = User::where('email', $request->email)->first();

            if($user && Hash::check($request['password'], $user->password)){
                $token = Str::random(60);
                $user->api_token = $token;
                $user->save();
                return response()->json(['data'=> $user, 'token'=> $token], 200);
            } else{
                return response()->json(['message'=> 'Dados inválidos'],404);
            }
    }

    /**
     * Função que busca pelo usuário com o id informado e atualiza o campo api_token para null.
     */
    public function logout(Request $request){
        $this->validate($request, [
            'id' => 'required',
        ]) ;

        $user = User::where('id', $request->id)->first();

        if($user){
            $user->api_token = null;

            $user->save();
            return response()->json(['data'=> $user], 200);
        }
        
        return response()->json(['message'=> 'Error'],404);
    }
}
