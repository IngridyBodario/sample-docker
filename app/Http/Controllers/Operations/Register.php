<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Repository\UsersRepository;
use App\Services\DataValidator;

class Register extends Controller
{
    public $requiredParams = [
        "name",
        "document",
        "email",
        "password"
    ];

    public function run($request)
    {
        try {
            $validator = new DataValidator();
            $repo_users = new UsersRepository();
            $infos = $request->all();
            
            $validate = $validator->validateParams($infos, $this->requiredParams);
            if($validate['error'] == true) {
                throw new \Exception("Parametro ".$validate['return']." obrigatorio"); 
            }

            $validate = $validator->validateDocument($infos['document'], $request);
            if($validate['error'] == true) {
                throw new \Exception($validate['message']);
            }
            $infos['type_user'] = $validate['type_user'];
            $infos['document'] = $validate['document'];
            
            $return = $repo_users->verify($infos);
            if(count($return) > 0) {
                throw new \Exception("Ja existe usuario com o mesmo Email e CPF/CNPJ"); 
            }

            $id = $repo_users->insert($infos);
            $return = [
                "Message:" => "Usuario inserido",
                "User Name" => $infos['name'],
                "ID" => $id
            ];

            return array(
                "error" => false,
                "result" => $return
            );
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        }
    }
}
