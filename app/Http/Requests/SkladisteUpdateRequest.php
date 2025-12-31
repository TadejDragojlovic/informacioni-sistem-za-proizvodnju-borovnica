<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SkladisteUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'lokacija' => ['required', 'string'],
            'kapacitet' => ['required', 'integer'],
            'temperatura' => ['required', 'numeric', 'between:-999.99,999.99'],
            'trosak' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
        ];
    }
}
