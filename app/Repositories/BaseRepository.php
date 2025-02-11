<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use Auth;

class BaseRepository
{
    /**
     * Método create genérico
     */
    public function create($model, array $payload)
    {
        DB::beginTransaction();
        try {
            $result = $model::create($payload);
            DB::commit();
            return $result;
        } catch (\Throwable $e) {
            dd($e->getMessage());
            DB::rollBack();
        }
	}

    /**
     * Método genérico para listar todos
     */
    public function getAll($model)
    {
        return $model::get();
	}

    /**
     * Método genérico para listar um elemento
     */
    public function getOne($model, int $id)
    {
        return $model::find($id);
	}

    /**
     * Visualizar um ou mais investimento de um investidor
     */
    public function viewInvestment($model, int $id = null)
    {
        $query = $model::select(
            'users.name as investor',
            'investments.status',
            'investments.amount as initial_amount',
            'investments.date as investment_date',
            'gains.value as gain_percentage'
            )
            ->join('users', 'users.id', 'investments.users_id')
            ->join('gains', 'gains.id', 'investments.gains_id');

        if (!is_null($id)) {
            $query->where('investments.id', $id);
            return $query->first();
        } else {
            $query->where('users.id', Auth::user()->id);
            return $query->get();
        }
	}
}
