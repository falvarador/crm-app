<?php

namespace App\Modules\Deals\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDealStageRequest extends FormRequest
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
            'stage' => ['required', 'in:prospecto,propuesta,negociacion,ganada,perdida'],
        ];
    }
}
