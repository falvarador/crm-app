<?php

namespace App\Modules\Companies\Http\Requests;

use App\Modules\Core\Support\CrmAccess;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'tax_id' => ['nullable', 'string', 'max:30', 'unique:companies,tax_id'],
            'sector' => ['required', 'string', 'max:60'],
            'status' => ['required', 'string', 'max:40'],
            'location' => ['required', 'string', 'max:100'],
            'website' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'owner' => ['bail', 'required', 'integer', CrmAccess::ownerExists($this->user())],
            'mrr' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99', 'decimal:0,2'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Ingresa la razón social o nombre de la empresa.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'sector.required' => 'Selecciona el sector de la cuenta.',
            'status.required' => 'Selecciona el estado comercial.',
            'location.required' => 'Indica la ciudad o país de la empresa.',
            'owner.required' => 'Asigna un responsable comercial.',
        ];
    }
}
