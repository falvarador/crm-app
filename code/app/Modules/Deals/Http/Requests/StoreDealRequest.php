<?php

namespace App\Modules\Deals\Http\Requests;

use App\Modules\Core\Support\CrmAccess;
use Illuminate\Foundation\Http\FormRequest;

class StoreDealRequest extends FormRequest
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
            'title' => ['required', 'string', 'min:3', 'max:120'],
            'company_name' => ['bail', 'required', 'integer', CrmAccess::companyExists($this->user())],
            'amount' => ['required', 'numeric', 'min:100', 'max:999999999999.99', 'decimal:0,2'],
            'stage' => ['required', 'in:prospecto,propuesta,negociacion,ganada,perdida'],
            'close_date' => ['required', 'date_format:Y-m-d'],
            'owner_name' => ['bail', 'required', 'integer', CrmAccess::ownerExists($this->user())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Ingresa el nombre de la oportunidad.',
            'company_name.required' => 'Selecciona o escribe la empresa asociada.',
            'amount.required' => 'Indica el valor estimado en USD.',
            'amount.min' => 'El valor mínimo estimado debe ser de $100 USD.',
            'stage.in' => 'Selecciona una etapa válida del pipeline.',
        ];
    }
}
