<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResurUpdateRequest extends FormRequest
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
            'naziv' => ['required', 'string'],
            'kolicina' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'trosak' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'proizvod_id' => ['required', 'integer', 'exists:Proizvods,id'],
        ];
    }
}
