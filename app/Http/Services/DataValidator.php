<?php 

namespace App\Http\Services;

class DataValidator
{
    public function validateParams($params, $requiredParams)
    {
        foreach ($requiredParams as $required) {
            if (!isset($params[$required]) || trim($params[$required]) == "") {
                return [
                    'error' => true,
                    'return' => $required
                ];
            }
        }
        return [
            'error' => false
        ];
    }

    public function validateDocument($document, $request) {
        try {
            $document = preg_replace("/[^0-9]/", "", $document);
            if(strlen($document) == 11) {
                $return = \Validator::make(
                    ['document' => $document],
                    ['document' => 'required|cpf']
                );
                if($return->fails() == true) {
                    throw new \Exception('CPF invalido');
                }
                $type_user = 1;
            } else {
                $return = \Validator::make(
                    ['document' => $document],
                    ['document' => 'required|cnpj']
                );
                if($return->fails() == true) {
                    throw new \Exception('CNPJ invalido');
                }
                $type_user = 2;
            }
            return [
                'error' => false,
                'type_user' => $type_user,
                'document' => $document
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }
}