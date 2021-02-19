<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class CurlService
{
    /**
     * 
     * @param string $url
     * @param string $metodo
     * @param array $params Parametros que serão enviado.
     * @param int $timeOut
     * @param array $auth Exemplo ['usuario', 'senha123']
     * @return array
     */
    static public function send($url, $metodo = 'POST', $params = [], $timeOut = 60, $auth = [])
    {
        try {
            $client = new Client();
            $response = $client->request(
                $metodo, 
                $url, 
                [
                    'auth' => $auth,
                    'timeout' => $timeOut,
                    'json' => $params
                ]
            );
            return [
                'message' => $response->getBody()->getContents(), 
                'status' => $response->getStatusCode()
            ];
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return [
                    'status' => $e->getResponse()->getStatusCode(), 
                    'message' => json_encode($e->getResponse()->getBody()->getContents())
                ];
            } 
            return [
                'status' => 500,
                'message' => strip_tags($e->getMessage())
            ];         
        }
    }
}