<?php

namespace App\Http\Requests\Auth;

use App\Models\Invitation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterResidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:32'],
            'code' => ['required', 'string', Rule::exists('invitations', 'code')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function invitation(): Invitation
    {
        return Invitation::query()->where('code', $this->string('code'))->firstOrFail();
    }
}
