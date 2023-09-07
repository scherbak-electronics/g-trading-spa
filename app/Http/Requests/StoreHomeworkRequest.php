<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHomeworkRequest extends FormRequest
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
            'symbol' => 'required',
            'timeframe' => 'nullable',
            'strategy' => 'nullable',
            'direction' => 'nullable',
            'title' => 'nullable',
            'description' => 'nullable',
            'additional_data' => 'nullable',
        ];
    }
}
