<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;     //indispensavel para autenticacao
use App\Models\User;

class AuthController extends Controller
{

        public function __construct(){
              // actions login(), create(), unauthorized(), metodos sem precisar autenticar
             // $this->middleware('auth:api', ['except' => 'login', 'create', 'unauthorized']);
        }

        public function unauthorized(){
            return response()->json(['error' => 'Não autorizado, erro de autenticação'], 401);
        }

        public function login(Request $request){
            $array = ['error' => ''];

            //pegue os dois campos que foram enviados pela rota::post login
            $email = $request->input('email');
            $password = $request->input('password');

            if($email && $password) {

                //fazendo o token atraves de email e token
                $token = auth()->attempt([
                    'email' => $email,
                    'password' => $password
                ]);

                // se nao tem token, ou errar senha/email, cai para o array de erro.
                if (!$token) {
                    // encaminhando para unauthorized 401 return $this->unauthorized();
                    //ou return $this->unauthorized();
                    $array['error'] = 'E-mail e/ou senhas errados';
                    return $array;
                }

                $array['token'] = $token;
                return $array;
            }
            else {
                $array['error'] = 'Dados não enviados';
                return $array;
            }
        }

        // Para fazer logout, "precisa estar autenticado"
        public function logout(){
             auth()->logout();  //ignorar token
             return ['error'=>''];
        }

        //gerando um outro Token com refresh
        public function refresh(){
             $token = auth()->refresh();
             return [
                 'error' => '',
                 'token' => $token
             ];
        }


        public function create(Request $request){
             $array = ['error' => ''];

             //pegar dados especificos com input que serao enviados para cadastro
             $name = $request->input('name');
             $email = $request->input('email');
             $password = $request->input('password');
             $birthdate = $request->input('birthdate');
             //restante dos dados sao nullable

            //confirmando que todos os dados estejam ok
            if($name && $email && $password && $birthdate){
                //se a data for invalida cai aqui
                if(strtotime($birthdate) === false){
                    $array['error'] = 'Data de nascimento invalida';
                    return $array;
                }


                //Buscar no banco algum registro similar ao email solicitado e fazer uma contagem
                $emailExists = User::where('email', $email)->count();

                //validacao saber se email ja existe, se tiver caia para email ja cadastrado
                if($emailExists === 0){
                     $hash = password_hash($password, PASSWORD_DEFAULT);

                     //novo User pegando os dados de dentro do model e passando mais um no users
                     $newUser = new User();
                     $newUser->name = $name;
                     $newUser->email = $email;
                     $newUser->password = $hash;
                     $newUser->birthdate = $birthdate;
                     //agora basta salvar no banco de dados users
                     $newUser->save();

                     //gerando um token com auth attempt
                     $token = auth()->attempt([
                         'email' => $email,
                         'password' => $password
                     ]);

                     //se o token nao for uma verdade caia nesse erro
                     if(!$token) {
                         $array['error'] = 'Ocorreu um erro';
                         return $array;
                     }
                     else {
                         $array['token'] = $token;
                     }
                }
                else{
                    $array['error'] = 'Email ja cadastrado no banco.';
                    return $array;
                }
            }
            else {
                $array['error'] = 'não enviou todos os campos';
                return $array;
            }

             return $array;
        }
}
