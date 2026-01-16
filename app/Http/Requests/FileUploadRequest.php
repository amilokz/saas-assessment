<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && (auth()->user()->isCompanyAdmin() || auth()->user()->isSupportUser());
    }

    public function rules()
    {
        return [
            'file' => 'required|file|max:10240', // 10MB max
            'is_public' => 'boolean',
        ];
    }
}