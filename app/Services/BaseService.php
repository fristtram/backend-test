<?php

namespace App\Services;

use App\Repositories\BaseRepository;

class BaseService
{
    /**
     * @var BaseRepository;
     */
    private $baseRepository;

    public function __construct(BaseRepository $baseRepository)
    {
        $this->baseRepository = $baseRepository;
    }

    public function create($model, $payload)
    {
        return $this->baseRepository->create($model, $payload);
    }

    public function getAll($model)
    {
        return $this->baseRepository->getAll($model);
    }

    public function getOne($model, $id)
    {
        return $this->baseRepository->getOne($model, $id);
    }

    public function viewInvestment($model, $id)
    {
        return $this->baseRepository->viewInvestment($model, $id);
    }
}
