<?php

namespace App\Modules\Contacts\Http\Requests;

use App\Modules\Core\Support\CrmAccess;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'min:2', 'max:60'],
            'last_name' => ['required', 'string', 'min:2', 'max:60'],
            'email' => ['required', 'email', 'max:120', 'unique:contacts,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'role' => ['required', 'string', 'max:80'],
            'company_name' => ['bail', 'required', 'integer', CrmAccess::companyExists($this->user())],
            'owner' => ['bail', 'nullable', 'integer', CrmAccess::ownerExists($this->user())],
            'source' => ['required', 'string', 'max:60'],
            'status' => ['required', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'El apellido es obligatorio.',
            'email.required' => 'Ingresa un correo electrónico corporativo.',
            'email.email' => 'Por favor ingresa un correo corporativo válido (ej. nombre@empresa.com).',
            'role.required' => 'Especifica el cargo o puesto comercial.',
            'company_name.required' => 'Indica la empresa vinculada.',
        ];
    }
}
