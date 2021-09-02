<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    private $loggedUser;

    public function __construct(){
        // todos os metodos precisam de uma autenticacao, exceto...
        //$this->middleware('auth:api');

        $this->loggedUser = auth()->user();    //salvando o usuario autenticado para usar nos metodos.
    }


    public function update(Request $request){
        $array = ['error' => ''];

        //receber todos os dados que podem ser alterados com update
        $name = $request->input('name');
        $email = $request->input('email');
        $birthdate = $request->input('birthdate');
        $city = $request->input('city');
        $work = $request->input('work');
        $password = $request->input('password');
        $password_confirm = $request->input('password_confirm');

        //pegar usuario no db, alterar as informaçoes e salvar o que foi alterado
        $user = User::find($this->loggedUser['id']);

        if($name){
            $user->name = $name;
        }


        //se email for enviado
        if($email){

            //verificar se o email e diferente ja existente do user
            if($email != $user->email){

                //busque no db um email similar a esse, caso nao tenha
                $emailExists = User::where('email', $email)->count();
                if($emailExists === 0){
                    $user->email = $email; // troque o email
                }
                else{
                    $array['error'] = 'Email ja existe';
                    return $array;
                }
            }
        }

        //se birthdate foi preenchido
        if($birthdate){
            //se for uma data valida
            if(strtotime($birthdate) === false){
                $array['error'] = 'Data de nascimento invalida';
            }
            $user->birthdate = $birthdate;
        }

        //se cidade for preenchida
        if($city) {
            $user->city = $city;
        }

        //se work foi prenchido
        if($work) {
            $user->work = $work;
        }

        if($password && $password_confirm){

            if($password === $password_confirm){
                //caso sejam iguais criei um hash
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $user->password = $hash;
            }
            else {
                $array['error'] = 'As senhas nao sao iguais';
            }
        }

         // verificando item a item o que foi solicitado para ser alterardo e salvar
        // finalizando o processo de alteracao, caso tenha alterado um ou nenhum dado
        $user->save();

        return $array;
    }

    public function updateAvatar(Request $request){
        $array = ['error' => 'Nenhum arquivo enviado'];
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];
        $image = $request->file('avatar');

        //se upload foi preenchido
        if($image) {
            // se estiver dentro de arquivos aceitaveis, faca isso
            if(in_array($image->getClientMimeType(), $allowedTypes)){

                $filename = md5(time().rand(0,999)).'.jpg';
                $destPath = public_path('/media/avatars');
                $img = Image::make($image->path())
                    ->fit(200,200)
                    ->save($destPath.'/'.$filename);

                $user = User::find($this->loggedUser['id']); //?*
                $user->avatar = $filename;
                $user->save();

                $array['url'] = url('/media/avatars/'.$filename);
            }
            else {
                $array['error'] = 'arquivo nao suportado';
            }
        }

        return $array;
    }

    public function updateCover(Request $request){
        $array = ['error' => '...'];

        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];
        $image = $request->file('cover');

        //se upload foi preenchido
        if($image) {
            // se estiver dentro de arquivos aceitaveis, faca isso
            if(in_array($image->getClientMimeType(), $allowedTypes)){

                $filename = md5(time().rand(0,999)).'.jpg';
                $destPath = public_path('/media/covers');
                $img = Image::make($image->path())
                    ->fit(850,310)
                    ->save($destPath.'/'.$filename);

                $user = User::find($this->loggedUser['id']); //?*
                $user->cover = $filename;
                $user->save();

                $array['url'] = url('/media/covers/'.$filename);
            }
            else {
                $array['error'] = 'arquivo nao suportado';
            }
        }

        return $array;
    }

    public function read($id = false){
         // get api/user
        //  get api/user/123

        $array = ['error' => ''];

        if($id){
            $info = User::find($id);
            if(!$info){
                $array['error'] = 'Usuario nao existe';

                return $array;
            }
        }
        else{
            $info = $this->loggedUser;
        }

        $info['avatar'] = url('media/avatars/'.$info['avatar']);
        $info['cover'] = url('media/covers/'.$info['cover']);

        $info['me'] = ($info['id'] == $this->loggedUser['id']) ? true : false;

        $array['data'] = $info;
        return $array;
    }
}
