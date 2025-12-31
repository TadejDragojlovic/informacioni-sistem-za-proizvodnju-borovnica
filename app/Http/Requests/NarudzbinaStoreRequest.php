<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NarudzbinaStoreRequest extends FormRequest
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
            'datum_narudzbine' => ['required', 'date'],
            'ukupna_cena' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'status' => ['required', 'in:kreirana,potvrdjena,u_obradi,otpremljena,isporucena,otkazana,vracena'],
            'user_id' => ['required', 'integer', 'exists:Users,id'],
        ];
    }
}
