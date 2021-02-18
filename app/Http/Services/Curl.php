<?php

namespace App\Http\Services;

use Exception;

class Curl
{
    public function connection($params)
    {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $params['url'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_ENCODING => "",
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{$params['postfield']}"
            ));
            $response = curl_exec($curl);
            if (curl_error($curl)) {                
                throw new Exception("Erro cURL. ".curl_error($curl)); 
            }
            curl_close($curl);
            
            return array(
                "erro" => 0,
                "resultado" => $response
            );
        } catch (Exception $e) {
            \Log::error($e);
            return array(
                "erro" => 1,
                "mensagem" => $e);
        }
    }
}