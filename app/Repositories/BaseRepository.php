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
}
