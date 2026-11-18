<?php

namespace App\Modules\Activities\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'company_name' => ['required', 'string', 'max:120'],
            'contact_name' => ['nullable', 'string', 'max:80'],
            'owner' => ['nullable', 'string', 'max:80'],
            'schedule_label' => ['required', 'string', 'max:60'],
            'notes' => ['nullable', 'string', 'max:600'],
            'completed' => ['nullable'],
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
