<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Gain;
use App\Models\Investment;
use App\Services\BaseService;
use App\Http\Requests\InvestmentRequest;
use App\Http\Requests\GainRequest;
use Auth;
use Carbon\Carbon;

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

    public function createInvestment(InvestmentRequest $request)
    {
        $userID = Auth::user()->id;
        $today = Carbon::today();
        $dateInvest = new Carbon($request->get('date'));
        $gain = $this->baseService->getOne($this->gain, $request->get('gains_id'));
        if (!is_null($gain)) {
            $payload = [
                'users_id' => $userID,
                'gains_id' => $gain->id,
                'gains_id' => $request->get('gains_id'),
                'amount' => $request->get('amount'),
                'date' => $dateInvest->toDateString(),
            ];
            if ($dateInvest->toDateString() <= $today->toDateString()) {
                $data = $this->baseService->create($this->investment, $payload);
                if ($data) {
                    return response()->json([
                        'success' => true,
                        'data' => $data
                    ], Response::HTTP_CREATED);
                }
                $message = 'Request cannot be processed';
            } else {
                $message = 'Cannot apply the investment in the future';
            }
        } else {
            $message = 'Integrity constraint violation: foreign key constraint of gain fails';
        }

        return response()->json([
            'success' => false,
            'message' => $message
        ], Response::HTTP_BAD_REQUEST);
    }

    public function creatGain(GainRequest $request)
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
