<?php

namespace App\Http\Requests;

use App\Models\Proizvod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ProizvodUpdateRequest extends FormRequest
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
            'naziv' => ['required', 'string', 'max:100'],
            'opis' => ['required', 'string'],
            'sorta_id' => ['required', 'integer', 'exists:sortas,id'],
            'neto_kolicina_g' => ['required', 'integer', 'min:1'],
            'cena' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'aktivan' => ['required', 'boolean'],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $proizvod = $this->route('proizvod');

                if (! $proizvod instanceof Proizvod || $validator->errors()->has('sorta_id')) {
                    return;
                }

                if ($proizvod->sorta_id !== $this->integer('sorta_id')
                    && $proizvod->narudzbinaStavkas()->exists()) {
                    $validator->errors()->add(
                        'sorta_id',
                        'Sortu proizvoda koji je već korišćen u narudžbini nije moguće promeniti.'
                    );
                }
            },
        ];
    }
}
