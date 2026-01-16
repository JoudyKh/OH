<?php

namespace App\Http\Requests\Api\General\Auth;

use App\Traits\HandlesValidationErrorsTrait;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    use HandlesValidationErrorsTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'university_number' => 'nullable|exists:users,university_number',
            'password' => 'required'

        ];
    }
    public function messages()
    {
        return [
            'university_number.exists' => 'الرقم الجامعي المدخل غير صحيح.'
        ];
    }
}
