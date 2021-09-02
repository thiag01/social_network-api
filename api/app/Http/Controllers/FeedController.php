<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\User;
use App\Models\UserRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Image;


class FeedController extends Controller
{
    private $loggedUser;

    public function __construct(){
        // todos os metodos precisa de uma autenticacao
        //$this->middleware('auth:api');

        $this->loggedUser = auth()->user();    //salvando o usuario autenticado para usar nos metodos.
    }

    //criando um feed
    public function create(Request $request){

        $array = ['error' => ''];
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];

        $type = $request->input('type');   //aqui vai o tipo de post texto/photo
        $body = $request->input('body');   // aqui vai texto
        $photo = $request->file('photo');  //aqui a url da imagem

        //caso type seja preenchido
        if($type){
            //escolha o tipo de post text/photo
            switch($type) {
                //filtrar o body se for text/photo
                case 'text':
                      if(!$body){
                          $array['error'] = 'Texto nao enviado';
                          return $array;
                      }
                    break;

                case 'photo':
                    if($photo){
                        if(in_array($photo->getClientMimeType(), $allowedTypes)){
                             $filename = md5(time().rand(0,9999)).'.jpg';
                             $destPatch = public_path('/media/uploads');

                             $img = Image::make($photo->path())
                                 ->resize(800, null, function($constraint){
                                     $constraint->aspectRatio();
                                 })
                                 ->save($destPatch.'/'.$filename);

                                 $body = $filename;
                        }
                        else{
                            $array['error'] = 'Arquivo nao suportado';
                            return $array;
                        }
                    }
                    else {
                        $array = ['error' => 'arquivo nao enviado'];
                    }
                    break;

                default:
                    $array['error'] = 'Tipo de post invalido';
                    break;
            }

            if($body) {
                $newPost = new Post();
                //selecionando o id do usuario para pegar o posts
                $newPost->id_user = $this->loggedUser['id'];  //pegando o usuario que esta logado com o token, no caso e o id 3
                $newPost->type = $type;
                $newPost->created_at = date('Y-m-d H:i:s');
                $newPost->body = $body;     // texto, url, photo, etc
                $newPost->save();
            }
        }
        else {
            $array = ['error' => 'dados nao enviados'];
        }

        return $array;
    }

    public function read(Request $request){
        $array = ['error' => ''];

        $page = intval($request->input('page'));
        $perPage = 2;

        // 1- Pegar a lista de usuarios que eu sigo
        $users = [];


        $userList = UserRelation::where('user_from', $this->loggedUser['id'])->get();

        foreach($userList as $userItem) {
            $users[] = $userItem['user_to'];
        }

        $users[] = $this->loggedUser['id'];


        // 2- Pegar os posts dessa galera Ordenado pela data

        // queryExemplo - SELECT * FROM posts WHERE id_user IN (1,2,3,4,100) ORDER BY created_at DESC LIMIT 0, 2;
        $postList = Post::whereIn('id_user', $users)
            ->orderBy('created_at', 'desc')
            ->offset($page * $perPage)
            ->limit($perPage)
            ->get();

             //quantidade de posts
            $total = Post::whereIn('id_user', $users)-> count();
            $pageCount = ceil($total / $perPage);


        // 3- Preencher as informações adicionais
        $posts = $this->_postListToObject($postList, $this->loggedUser['id']);

        $array['posts'] = $posts;
        $array['pageCount'] = $pageCount;
        $array['currentPage'] = $page;

        return $array;
    }

    public function userFeed(Request $request, $id = false){
        $array = ['error' => ''];

        if($id == false){
            $id = $this->loggedUser['id'];
        }

        $page = intval($request->input('page'));
        $perPage = 2;

        //pegar os posts do usuario ordenado pela data
        $postList = Post::where('id_users', $id)
            ->orderBy('created_at', 'desc')
            ->offset($page * $perPage)
            ->limit($perPage)
            ->get();

            $total = Post::where('id_user', $id)->count();
            $pageCount = ceil($total / $perPage);

            $posts = $this->_postListToObject($postList, $this->loggedUser['id']);

            $array['posts'] = $posts;
            $array['pageCount'] = $pageCount;
            $array['currentPage'] = $page;

            return $array;
    }

    private function _postListToObject($postList, $loggedId){
        foreach ($postList as $postKey => $postItem){
            //verificar se o post e o meu
            if($postItem['id_user'] == $loggedId){
                $postList[$postKey]['mine'] = true;
            }
            else {
                $postList[$postKey]['mine'] = false;
            }
            //prencher informações do usuario
            $userInfo = User::find($postItem['id_user']);
            $userInfo['avatar'] = url('media/avatars/'.$userInfo['avatar']);
            $userInfo['cover'] = url('media/covers/'.$userInfo['cover']);
            $postList[$postKey]['user'] = $userInfo;

            //preeencher informaçoes de curtidas
            $likes = PostLike::where('id_post', $postItem['id'])->count();
            $postList[$postKey]['likecount'] = $likes;

            $isLiked = PostLike::where('id_post', $postItem['id'])
                ->where('id_user', $loggedId)
                ->count();
            $postList[$postKey]['liked'] = ($isLiked > 0) ? true : false;

            //preencher informaçoes de Comments
            $comments = PostComment::where('id_post', $postItem['id'])->get();
            foreach($comments as $commentKey => $comment){
                $user = User::find($comment['id_user']);
                $user['avatar'] = url('media/avatars/'.$userInfo['avatar']);
                $user['cover'] = url('media/covers/'.$user['cover']);
                $comments[$commentKey]['user'] = $user;
            }
            $postList[$postKey]['comments'] = $comments;
        }
        return $postList;
    }
}
