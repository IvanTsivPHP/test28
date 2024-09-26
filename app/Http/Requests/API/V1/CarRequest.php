<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;

class CarRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           'brand_id' => 'required|exists:brands,id',
            'model_id' => 'required|exists:car_models,id',
            'year' => 'nullable|integer',
            'mileage' => 'nullable|integer',
            'color' => 'nullable|string|max:255',
        ];
    }
}
