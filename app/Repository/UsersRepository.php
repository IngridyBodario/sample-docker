<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsersRepository
{
    public function verify($params) 
    {
        $result = DB::connection('pgsql')
            ->table('users')
            ->where([
                ['users.email', '=', $params['email']],
                ['users.document', '=', $params['document']]
            ])
            ->get();
        return $result;
    }

    public function insert($params) 
    {
        $result = DB::connection('pgsql')
            ->table('users')
            ->insertGetId([
                'name' => $params['name'],
                'document' => $params['document'],
                'email' => $params['email'],
                'password' => bcrypt($params['password']),
                'type_user' => $params['type_user']
            ]);
        return $result;
    }

    public function searchUser($id) 
    {
        $result = DB::connection('pgsql')
            ->table('users')
            ->where('users.id', '=', $id)
            ->get();
        return $result;
    }

    public function updateBalance($balance, $id_user)
    {
        return DB::connection('pgsql')
            ->table('users')
            ->whereIn('users.balance', $balance)
            ->update(['id' => $id_user]);
    }
}