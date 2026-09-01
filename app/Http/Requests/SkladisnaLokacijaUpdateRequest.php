<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SkladisnaLokacijaUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'naziv' => [
                'required',
                'string',
                'max:255',
                Rule::unique('skladisna_lokacija', 'naziv')
                    ->where('skladiste_id', $this->route('skladiste')->id)
                    ->ignore($this->route('skladisnaLokacija')),
            ],
            'opis' => ['required', 'string'],
            'aktivna' => ['required', 'boolean'],
        ];
    }
}
