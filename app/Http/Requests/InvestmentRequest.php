<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvestmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'gains_id' => 'required|int',
            'amount' => 'required|min:1|regex:/[\d]{1}.[\d]{2}/',
            'date' => 'required|date',
        ];
    }
}
