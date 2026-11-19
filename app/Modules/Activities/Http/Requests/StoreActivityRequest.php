<?php

namespace App\Modules\Activities\Http\Requests;

use App\Modules\Core\Support\CrmAccess;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivityRequest extends FormRequest
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
            'type' => ['required', 'in:llamada,correo,reunion,tarea,nota'],
            'subject' => ['required', 'string', 'min:3', 'max:160'],
            'company_name' => ['bail', 'required', 'integer', CrmAccess::companyExists($this->user())],
            'contact_name' => ['bail', 'nullable', 'integer', Rule::exists('contacts', 'id')->where(function ($query): void {
                $query->where('company_id', $this->integer('company_name'))->whereNull('deleted_at');
                if ($this->user()->role !== 'admin') {
                    $query->where('owner_id', $this->user()->id);
                }
            })],
            'owner' => ['bail', 'nullable', 'integer', CrmAccess::ownerExists($this->user())],
            'schedule_label' => ['required_unless:type,nota', 'nullable', 'date_format:Y-m-d\TH:i'],
            'notes' => ['nullable', 'string', 'max:600'],
            'completed' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'subject.required' => 'Describe el asunto de la interacción o tarea.',
            'subject.min' => 'El asunto debe tener al menos 3 caracteres.',
            'company_name.required' => 'Vincula una empresa o cuenta comercial.',
            'schedule_label.required' => 'Indica la fecha u hora programada.',
        ];
    }
}
