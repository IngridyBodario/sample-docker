<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionRepository
{
    public function insert($params) 
    {
        $result = DB::connection('pgsql')
            ->table('transaction')
            ->insertGetId([
                'payer' => $params['payer'],
                'payee' => $params['payee'],
                'value' => $params['value']
            ]);
        return $result;
    }

    public function updateTransaction($status, $id)
    {
        return DB::connection('pgsql')
            ->table('transaction')
            ->where('transaction.id', '=', $id)
            ->update(['transaction.status' => $status]);
    }
}