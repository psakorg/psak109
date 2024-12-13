<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules()
{
    $user = $this->user()->user_id; // Mengambil user_id yang sesuai

    return [
        'name' => ['required', 'string', 'max:255'],
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('tbl_users')->ignore($user, 'user_id'), // Mengabaikan user_id pengguna yang sedang diperbarui
        ],
    ];
}

}
