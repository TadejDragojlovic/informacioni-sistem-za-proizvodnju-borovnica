<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LotPrijemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'skladisna_lokacija_id' => ['required', 'integer', 'exists:skladisna_lokacija,id'],
        ];
    }
}
