<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvitationRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->isCompanyAdmin();
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'role_id' => 'required|exists:roles,id',
        ];
    }
}