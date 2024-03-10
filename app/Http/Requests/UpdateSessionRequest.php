<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'strategy_code' => 'nullable',
            'total_investment' => 'nullable',
            'total_profit' => 'nullable',
            'main_level_price' => 'nullable',
            'entry_point_price' => 'nullable',
            'take_profit_price' => 'nullable',
            'take_profit_timeout' => 'nullable',
            'stop_loss_price' => 'nullable',
            'trailing_delta' => 'nullable',
            'stop_loss_safe_time' => 'nullable'
        ];
    }
}
