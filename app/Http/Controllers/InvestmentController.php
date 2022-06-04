<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Gain;
use App\Models\Investment;
use App\Services\BaseService;
use Auth;

class InvestmentController extends Controller
{
    private $gain;
    private $investment;
    private $baseService;
    /**
    * @return void
    */
    public function __construct(
        Gain $gain,
        Investment $investment,
        BaseService $baseService
        )
    {
        $this->gain = $gain;
        $this->investment = $investment;
        $this->baseService = $baseService;
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->baseService->getAll($this->investment)
        ], Response::HTTP_OK);
    }

    public function creatGain(Request $request)
    {
        $payload = [
            'value' => $request->get('value'),
        ];

        $data = $this->baseService->create($this->gain, $payload);
        return response()->json([
            'success' => true,
            'data' => $data
        ], Response::HTTP_CREATED);
    }

    public function getGain()
    {
        return response()->json([
            'success' => true,
            'data' => $this->baseService->getAll($this->gain)
        ], Response::HTTP_OK);
    }
}
