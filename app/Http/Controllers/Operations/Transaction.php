<?php

namespace App\Http\Controllers\Operations;

use App\Repository\TransactionRepository;
use App\Repository\UsersRepository;
use App\Services\CurlService;

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
            $curl = new CurlService();
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

            $id = $repo_transaction->insert($infos);
            $return = $curl->send(env('MOCKY_TRANSACTION'), 'POST', $infos);
            if($return['status'] == 500) {
                $repo_transaction->updateTransaction($retorno_api['message'],$id);
                throw new Exception($retorno_api['message']);
            }
            
            $wallet_value = empty($return_payer->balance) ? 0 : (float)$return_payer->balance;
            $balance_payer = (float)$wallet_value - (float)$infos['value'];
            $return = $repo_user->updateBalance($balance_payer, $infos['payer']);

            $wallet_value = empty($return_payee->balance)? 0 :(float)$return_payee->balance;
            $balance_payee = (float)$wallet_value + (float)$infos['value'];
            $repo_user->updateBalance($balance_payee, $infos['payee']);

            $return = $curl->send(env('MOCKY_NOTIFICATION'), 'POST', $infos);
            if($return['status'] == 500) {
                $repo_transaction->updateTransaction($retorno_api['message'],$id);
                throw new Exception($retorno_api['message']);
            }

            $repo_transaction->updateTransaction('success',$id);
            return array(
                "error" => false,
                "result" => 'ok'
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
