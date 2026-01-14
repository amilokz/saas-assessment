<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isCompanyAdmin();
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered as a user.',
        ];
    }
}