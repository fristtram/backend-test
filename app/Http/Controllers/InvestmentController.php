<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Gain;
use App\Models\Investment;
use App\Models\Withdrawal;
use App\Services\BaseService;
use App\Http\Requests\InvestmentRequest;
use App\Http\Requests\GainRequest;
use App\Http\Requests\InvestmentPaymentRequest;
use App\Http\Requests\AllInvestmentPaymentRequest;
use App\Http\Requests\WithdrawalsRequest;
use Auth;
use Carbon\Carbon;

class InvestmentController extends Controller
{
    private $gain;
    private $investment;
    private $baseService;
    private $withdrawal;
    /**
    * @return void
    */
    public function __construct(
        Gain $gain,
        Investment $investment,
        BaseService $baseService,
        Withdrawal $withdrawal
        )
    {
        $this->gain = $gain;
        $this->investment = $investment;
        $this->withdrawal = $withdrawal;
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

    public function getAllInvestment(AllInvestmentPaymentRequest $request)
    {
        $datePayment = new Carbon($request->get('date_payment'));
        $investments = $this->baseService->viewInvestment($this->investment);
        if (!is_null($investments)) {
            $allInvest = [];
            foreach ($investments as $value) {
                $investmentDate = new Carbon($value->investment_date);
                if ($value->investment_date <= $datePayment->toDateString()) {
                    $days = $datePayment->diffInDays($investmentDate);
                    if ($days >= 30) {
                        $amountMonth = floor($days/30);
                        $data = $this->gainCalculation(intval($amountMonth), $value);

                        $data->investor = $value->investor;
                        $data->investment_date = $value->investment_date;
                        $data->gain_percentage = $value->gain_percentage;
                        $allInvest[] = $data;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $allInvest
            ], Response::HTTP_OK);

        } else {
            $message = 'Integrity constraint violation: foreign key constraint of investment fails';
        }
        return response()->json([
            'success' => false,
            'message' => $message
        ], Response::HTTP_BAD_REQUEST);
    }

    public function getInvestment(InvestmentPaymentRequest $request)
    {
        $datePayment = new Carbon($request->get('date_payment'));
        $investment = $this->baseService->viewInvestment($this->investment, $request->get('invest_id'));
        if (!is_null($investment)) {
            $investmentDate = new Carbon($investment->investment_date);
            if ($investment->investment_date <= $datePayment->toDateString()) {
                $days = $datePayment->diffInDays($investmentDate);
                if ($days >= 30) {
                    $amountMonth = floor($days/30);
                    $data = $this->gainCalculation(intval($amountMonth), $investment);

                    $data->investor = $investment->investor;
                    $data->investment_date = $investment->investment_date;
                    $data->gain_percentage = $investment->gain_percentage;

                    return response()->json([
                        'success' => true,
                        'data' => $data
                    ], Response::HTTP_OK);
                } else {
                    $message = 'Investment less than 1 month';
                }
            } else {
                $message = 'Investment less than 1 month';
            }
        } else {
            $message = 'Integrity constraint violation: foreign key constraint of investment fails';
        }
        return response()->json([
            'success' => false,
            'message' => $message
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Calculo de ganho conforme a retirada desejada
     */
    public function gainCalculation($amountMonth, $investment)
    {
        $initial = $investment->initial_amount;
        $percentage = $investment->gain_percentage;

        $loop = 1;
        $amountWithGain = $initial;
        while ($loop <= $amountMonth) {
            $gainMonth = $amountWithGain*$percentage;
            $amountWithGain = $gainMonth + $amountWithGain;
            $loop++;
        }

        $gainTotal = $amountWithGain - $initial;
        $taxation = $this->taxationCalculation($gainTotal, $amountMonth);

        $month = ($amountMonth > 1) ? ' months' : ' month';

        return (object)[
            'initial_amount' => $initial,
            'amount_with_gain' => number_format($amountWithGain,2,",","."),
            'taxation' => number_format($taxation,2,",","."),
            'net_amount' => number_format(($amountWithGain - $taxation),2,",","."),
            'investment_period' => $amountMonth . $month
        ];
    }

    /**
     * Calculo da tributação (imposto)
     */
    public function taxationCalculation($gainTotal, $amountMonth)
    {
        $tax = 0;
        if ($amountMonth < 12) {
            $tax = $gainTotal*0.225;
        } elseif($amountMonth >= 12 && $amountMonth <= 24) {
            $tax = $gainTotal*0.185;
        } else {
            $tax = $gainTotal*0.15;
        }
        return $tax;
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

    public function getWithdrawal()
    {
        return response()->json([
            'success' => true,
            'data' => $this->baseService->getAll($this->withdrawal)
        ], Response::HTTP_OK);
    }

    public function withdrawalInvestment(WithdrawalsRequest $request)
    {
        $dateWithdrawal = new Carbon($request->get('date_withdrawal'));
        $investment = $this->baseService->viewInvestment($this->investment, $request->get('investment_id'));

        if (!is_null($investment)) {
            if ($investment->status == 0) {
                $investmentDate = new Carbon($investment->investment_date);
                if ($investment->investment_date <= $dateWithdrawal->toDateString()) {
                    $days = $dateWithdrawal->diffInDays($investmentDate);
                    if ($days >= 30) {
                        $amountMonth = floor($days/30);
                        $data = $this->gainCalculation(intval($amountMonth), $investment);

                        // Registrar retirada
                        $data->gain_percentage = $investment->gain_percentage;
                        $taxation = str_replace('.', '', $data->taxation);
                        $taxation = str_replace(',', '.', $taxation);
                        $net_amount = str_replace('.', '', $data->net_amount);
                        $net_amount = str_replace(',', '.', $net_amount);

                        $payload = [
                            'investment_id' => $request->get('investment_id'),
                            'taxation' => $taxation,
                            'amount_withdrawn' => $net_amount
                        ];
                        $data = $this->baseService->create($this->withdrawal, $payload);

                        // Retirar o investimento
                        $investment = $this->baseService->getOne($this->investment, $request->get('investment_id'));
                        $dataUpdate = [
                            'status' => 1,
                            'amount' => 0.00
                        ];
                        $investment->update($dataUpdate);

                        return response()->json([
                            'success' => true,
                            'data' => $data
                        ], Response::HTTP_OK);
                    } else {
                        $message = 'Investment less than 1 month';
                    }
                } else {
                    $message = 'Investment less than 1 month';
                }
            } else {
                $message = 'This investment has already been withdrawn';
            }

        } else {
            $message = 'Integrity constraint violation: foreign key constraint of investment fails';
        }
        return response()->json([
            'success' => false,
            'message' => $message
        ], Response::HTTP_BAD_REQUEST);
        return response()->json([
            'success' => true,
            'data' => $data
        ], Response::HTTP_CREATED);
    }
}
