<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    /** 
     * Função que recebe e manipula as informações recebidas pelas rotas.
     * @param array $request
     * @param array $id
     * @return array
     */
    public function api(Request $request, $id)
    {
        try {
            $path_class = "App\Http\Controllers\Operations\\{$id}";
            if (!class_exists($path_class)){
                throw new \Exception("Nao existe a classe $id"); 
            }
            $class = new $path_class();

            $return = $class->run($request);
            if($return['error'] == true) {
                throw new \Exception($retorno['message']);
            }
            return $return;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        }
    }
}
