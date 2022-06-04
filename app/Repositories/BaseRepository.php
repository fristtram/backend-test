<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use Auth;

class BaseRepository
{
    public function create($model, array $payload)
    {
        DB::beginTransaction();
        try {
            $result = $model::create($payload);
            DB::commit();
            return $result;
        } catch (\Throwable $e) {
            DB::rollBack();
        }
	}

    public function getAll($model)
    {
        return $model::get();
	}

    public function getOne($model, int $id)
    {
        return $model::find($id);
	}

    public function viewInvestment($model, int $id = null)
    {
        $query = $model::select(
            'users.name as investor',
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
