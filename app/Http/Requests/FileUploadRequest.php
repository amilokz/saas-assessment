<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && ($user->isCompanyAdmin() || $user->isSupportUser());
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|max:10240', // 10MB max
            'is_public' => 'boolean',
        ];
    }
}