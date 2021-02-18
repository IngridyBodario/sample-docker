<?php

namespace App\Http\Services;

use App\Repository\TransactionRepository;
use App\Repository\UsersRepository;
use App\Http\Services\Curl;

class Transaction
{
    public $requiredParams = [
        "value",
        "payer",
        "payee"
    ];

    public function run($request) {
        try {
            $repo_transaction = new TransactionRepository();
            $repo_user = new UsersRepository();
            $curl = new Curl();
            $infos = $request->all();
    
            $return_payer = $repo_user->searchUser($infos['payer']);
            if(count($return_payer) == 0) {
                throw new \Exception('ID Pagador invalido');
            }

            $return_payee = $repo_user->searchUser($infos['payee']);
            if(count($return_payee) == 0) {
                throw new \Exception('ID Beneficiario invalido');
            }

            if($return_payer[0]->type_user == 2) {
                throw new \Exception('Logistas nao podem ser pagadores');
            }

            $return = $repo_transaction->insert($infos);
            $parametros['postfield'] = '{"value":"'.$infos['value'].'",
                "payer":"'.$infos['payer'].'",
                "payee":"'.$infos['payee'].'"}';
            $parametros['url'] = env('MOCKY_TRANSACTION');
            $return = $curl->conexao();
            if($retorno_api['erro'] == 1) {
                throw new Exception($retorno_api['mensagem']);
            }

            $balance_payer = (float)$return_payer->balance - (float)$infos->balance;
            $repo_user->updateBalance($balance_payer, $infos['payer']);

            $balance_payee = (float)$return_payee->balance + (float)$infos->balance;
            $repo_user->updateBalance($balance_payee, $infos['payee']);
            
            $parametros['postfield'] = '{"value":"'.$infos['value'].'",
                "payer":"'.$infos['payer'].'",
                "payee":"'.$infos['payee'].'"}';
            $parametros['url'] = env('MOCKY_NOTIFICATION');
            $return = $curl->conexao();
            if($retorno_api['erro'] == 1) {
                throw new Exception($retorno_api['mensagem']);
            }
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        }
    }
}
