<?php

namespace App\Http\Requests\V2\Feature;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpsertFeaturePhysicalInformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updatePhysicalInformation', $this->route('feature'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'group_name' => ['required', 'string', 'max:255'],
            'active_company' => ['required', 'string', 'max:255'],
            'physical_address' => ['required', 'string', 'max:500'],
            'physical_postal_code' => ['required', 'ir_postal_code'],
            'postal_address' => ['required', 'ir_postal_code'],
            'establishment_goal' => ['required', 'string', 'max:1000'],
            'isic_code_id' => ['nullable', 'integer', 'exists:isic_codes,id', 'required_without:activity'],
            'activity' => ['nullable', 'string', 'max:255', 'required_without:isic_code_id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->filled('isic_code_id') && $this->filled('activity')) {
                $validator->errors()->add(
                    'activity',
                    'Provide either isic_code_id or activity, not both.',
                );
            }
        });
    }
}
