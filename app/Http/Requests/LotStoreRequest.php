<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LotStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sorta_id' => ['required', 'integer', 'exists:sortas,id'],
            'parcela_id' => ['required', 'integer', 'exists:parcelas,id'],
            'datum_berbe' => ['required', 'date'],
            'pocetna_kolicina_g' => ['required', 'integer', 'min:1'],
            'napomena' => ['nullable', 'string'],
        ];
    }
}
