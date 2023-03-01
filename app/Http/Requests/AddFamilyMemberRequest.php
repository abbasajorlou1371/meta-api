<?php

namespace App\Http\Requests;

use App\Enums\FamilyRelationships;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class AddFamilyMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user' => 'required|integer|exists:users,id',
            'relationship' => [
                'required',
                'string',
                new Enum(FamilyRelationships::class)
            ],
            'permissions' => [
                'required_if:relationship,offspring',
                'array',
                'min:10',
                'required_array_keys:BFR,SF,W,JU,DM,PIUP,PITC,PIC,ESOO,COTB'
            ],
            'permissions.*' => 'integer|boolean'
        ];
    }
}
