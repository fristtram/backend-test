<?php

namespace App\Services;

use App\Repositories\BaseRepository;

class BaseService
{
    /**
     * @var BaseRepository;
     */
    private $baseRepository;

    /**
     * @return void
     */
    public function __construct(BaseRepository $baseRepository)
    {
        $this->baseRepository = $baseRepository;
    }

    /**
     * Chamada de método genérico create
     */
    public function create($model, $payload)
    {
        return $this->baseRepository->create($model, $payload);
    }

    /**
     * Chamada de método genérico listar todos
     */
    public function getAll($model)
    {
        return $this->baseRepository->getAll($model);
    }

    /**
     * Chamada de método genérico listar um elemento
     */
    public function getOne($model, $id)
    {
        return $this->baseRepository->getOne($model, $id);
    }

    /**
     * Chamada de método genérico para visualizar um ou mais investimento de um investidor
     */
    public function viewInvestment($model, $id = null)
    {
        return $this->baseRepository->viewInvestment($model, $id);
    }
}
