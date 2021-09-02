<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    private $loggedUser;

    public function __construct(){
        // todos os metodos precisa de uma autenticacao
        // $this->middleware('auth:api');

        $this->loggedUser = auth()->user();    //salvando o usuario autenticado para usar nos metodos.
    }
}
