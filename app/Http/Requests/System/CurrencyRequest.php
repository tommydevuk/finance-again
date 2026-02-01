<?php

namespace App\Http\Requests\System;

use App\Enums\CurrencyTypeEnum;
use App\Models\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Currency::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('currencies', 'code')->ignore($this->currency),
            ],
            'symbol' => ['nullable', 'string', 'max:10'],
            'type' => ['required', Rule::enum(CurrencyTypeEnum::class)],
            'decimals' => ['required', 'integer', 'min:0', 'max:18'],
        ];
    }
}
