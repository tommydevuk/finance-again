<?php

namespace App\Http\Requests\System;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // $this->user is the route parameter name, hopefully. 
        // I will check routes definition later.
        $user = $this->route('user'); 
        return $this->user()->can('update', $user);
    }

    public function rules(): array
    {
        $user = $this->route('user');
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
