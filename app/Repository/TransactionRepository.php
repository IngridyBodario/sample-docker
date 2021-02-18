<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionRepository
{
    public function verify($params) 
    {
        $result = DB::connection('pgsql')
            ->table('users')
            ->where([
                ['users.email', '=', $params->email],
                ['users.document', '=', $params->document]
            ])
            ->get();
        return $result;
    }

    public function insert($params) 
    {
        $result = DB::connection('pgsql')
            ->table('transaction')
            ->insert([
                'payer' => $params->name,
                'payee' => $params->document,
                'status' => $params->email
            ]);
        return $result;
    }
}