<?php

namespace App\Http\Requests\Api\Admin\Admins;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**  
 * @OA\Schema(  
 *     schema="UpdateAdminRequest",  
 *     type="object",  
 *     @OA\Property(property="name", type="string", maxLength=255, example="Sample Name"),   
 *     @OA\Property(property="email", type="email", example="email@example.com"),   
 *     @OA\Property(property="password", type="string", example="password"),   
 *     @OA\Property(property="phone_number", type="string", example="+97243"),   
 *     @OA\Property(property="role", enum={"content_manager","project_manager","admin"}),   
 * )  
 */
class UpdateAdminRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users')->where(function ($query) {
                    return $query->whereNull('deleted_at'); // Check only active users  
                })->ignore($this->route('admin')->id), // Ignore the current user's ID  
            ],
            'password' => 'sometimes|string',
            'role' => 'sometimes|in:content_manager,project_manager,admin',
            'phone_number' => 'sometimes|string',

        ];
    }
}
