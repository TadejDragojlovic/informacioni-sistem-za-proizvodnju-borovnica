<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LotKorekcijaKolicineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'raspoloziva_kolicina_g' => ['required', 'integer', 'min:0'],
            'razlog' => ['required', 'string', 'max:1000'],
        ];
    }
}
