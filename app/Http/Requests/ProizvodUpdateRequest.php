<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProizvodUpdateRequest extends FormRequest
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
            'naziv' => ['required', 'string', 'max:100'],
            'opis' => ['nullable', 'string'],
            'kolicina' => ['required', 'integer'],
            'cena' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'skladiste_id' => ['required', 'integer', 'exists:Skladistes,id'],
        ];
    }
}
