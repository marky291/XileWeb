<?php

namespace App\Http\Requests;

use App\Ragnarok\Login;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'email',
                'max:39',
                Rule::unique(Login::class, 'email')->ignore($this->user()->account_id, 'account_id'),
            ],
        ];
    }
}
