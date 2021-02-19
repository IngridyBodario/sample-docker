<?php 

namespace App\Services;

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
            $type_document = (strlen($document) == 11) ? 'cpf' : 'cnpj';
            $return = \Validator::make(
                ['document' => $document],
                ['document' => 'required|'.$type_document]
            );
            if($return->fails() == true) {
                throw new \Exception($type_document.' invalido');
            }
            $type_user = (strlen($document) == 11) ? 1 : 2;
            
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