<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'name' => ['required', 'string', Rule::unique('users', 'name')->ignore(Auth::user()->id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore(Auth::user()->id)],
            'confirm_password' => ['required', 'string']
        ];
    }
}