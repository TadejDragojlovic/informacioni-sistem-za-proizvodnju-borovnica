<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProizvodStoreRequest extends FormRequest
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
            'naziv' => ['required', 'string', 'max:255'],
            'opis' => ['nullable', 'string'],
            'sorta_id' => ['required', 'integer', 'exists:sortas,id'],
            'neto_kolicina_g' => ['required', 'integer', 'min:1'],
            'cena' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'aktivan' => ['required', 'boolean'],
        ];
    }
}
