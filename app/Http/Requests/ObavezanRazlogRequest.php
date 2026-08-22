<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObavezanRazlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'razlog' => ['required', 'string', 'max:1000'],
        ];
    }
}
