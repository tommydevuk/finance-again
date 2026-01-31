<?php

namespace App\Http\Requests\System;

use App\Enums\PlatformTypeEnum;
use App\Models\Platform;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlatformRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Platform::class); // Or specific permissions
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'type' => ['required', Rule::enum(PlatformTypeEnum::class)],
            'slug' => [
                'nullable', 
                'string', 
                'max:255', 
                Rule::unique('platforms', 'slug')->ignore($this->platform)
            ],
        ];
    }
}
