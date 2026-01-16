<?php

namespace App\Http\Requests\Api\Admin\Admins;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**  
 * @OA\Schema(  
 *     schema="CreateAdminRequest",  
 *     type="object",  
 *     required={  
 *         "name", "email", "password", "role","phone_number",
 *     },  
 *     @OA\Property(property="name", type="string", maxLength=255, example="Sample Name"),   
 *     @OA\Property(property="email", type="email", example="email@example.com"),   
 *     @OA\Property(property="password", type="string", example="password"),   
 *     @OA\Property(property="phone_number", type="string", example="+97243"),   
 *     @OA\Property(property="role", enum={"content_manager","project_manager","admin"}),   
 * )  
 */
class CreateAdminRequest extends FormRequest
{
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
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where(function ($query) {
                    return $query->whereNull('deleted_at'); // Ensures to check only active users  
                }),

            ],
            'password' => 'required|string',
            'role' => 'required|in:content_manager,project_manager,admin',
            'phone_number' => 'required|string',
        ];
    }
}
