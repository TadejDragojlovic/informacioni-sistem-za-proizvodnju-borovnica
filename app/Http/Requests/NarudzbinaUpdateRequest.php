<?php

namespace App\Http\Requests;

use App\Enums\NarudzbinaStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NarudzbinaUpdateRequest extends FormRequest
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
            'status' => ['required', Rule::enum(NarudzbinaStatus::class)],
            'razlog' => [
                'required_if:status,'.NarudzbinaStatus::OTKAZANA->value,
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}
