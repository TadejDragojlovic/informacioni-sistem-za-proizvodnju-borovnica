<?php

namespace App\Http\Requests;

use App\Enums\KlasaKvaliteta;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LotKvalitetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'klasa_kvaliteta' => ['required', Rule::enum(KlasaKvaliteta::class)],
            'broj_dokumenta_kvaliteta' => ['required', 'string', 'max:255'],
        ];
    }
}
