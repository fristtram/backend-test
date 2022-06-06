<?php

namespace App\Http\Controllers;

use App\Services\BaseService;
use App\Models\User;
use Illuminate\Http\Response;

class UserController extends Controller
{
    private $user;
    private $baseService;

    /**
    *
    * @return void
    */
    public function __construct(User $user, BaseService $baseService)
    {
        $this->user = $user;
        $this->baseService = $baseService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Listar usuários de sistema
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->baseService->getAll($this->user)
        ], Response::HTTP_OK);
    }
}
