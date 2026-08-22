<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResurStoreRequest extends FormRequest
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
            'lot_id' => ['required', 'integer', 'exists:lots,id'],
            'naziv' => ['required', 'string', 'max:255'],
            'kolicina' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'jedinica_mere' => ['required', 'string', 'max:50'],
            'cena_po_jedinici' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'datum_upotrebe' => ['required', 'date'],
        ];
    }
}
