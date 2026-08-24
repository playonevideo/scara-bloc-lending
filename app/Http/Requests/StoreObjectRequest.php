<?php

namespace App\Http\Requests;

use App\Enums\ObjectCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreObjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'condition' => ['required', Rule::enum(ObjectCondition::class)],
            'max_borrow_days' => ['required', 'integer', 'min:1', 'max:365'],
            'requires_personal_handover' => ['sometimes', 'boolean'],
            'can_leave_at_door' => ['sometimes', 'boolean'],
            'special_conditions' => ['nullable', 'string', 'max:2000'],
            'usage_instructions' => ['nullable', 'string', 'max:2000'],
            'images' => ['nullable', 'array', 'max:6'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'titlu',
            'category_id' => 'categorie',
            'condition' => 'stare',
            'max_borrow_days' => 'perioadă maximă de împrumut',
        ];
    }
}
