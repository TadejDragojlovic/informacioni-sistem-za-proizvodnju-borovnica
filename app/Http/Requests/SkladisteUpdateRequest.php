<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'naziv' => [
                'required',
                'string',
                'max:255',
                Rule::unique('skladistes', 'naziv')->ignore($this->route('skladiste')),
            ],
            'lokacija' => ['required', 'string', 'max:255'],
            'mesecni_trosak' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'aktivan' => ['required', 'boolean'],
        ];
    }
}
