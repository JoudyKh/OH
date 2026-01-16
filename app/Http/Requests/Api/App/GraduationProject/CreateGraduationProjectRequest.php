<?php

namespace App\Http\Requests\Api\App\GraduationProject;

use Illuminate\Foundation\Http\FormRequest;

/**  
 * @OA\Schema(  
 *     schema="CreateGraduationProjectRequest",  
 *     type="object",  
 *     @OA\Property(property="name", type="string", example="Sample Name"),  
 *     @OA\Property(property="subject", type="string", example="Sample Name"),  
 *     @OA\Property(property="university_id", type="integer", example="1"),  
 *     @OA\Property(property="phone_number", type="string", example="Sample Name"),  
 * )  
 */
class CreateGraduationProjectRequest extends FormRequest
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
            'name' => 'required|string',
            'phone_number' => 'required|string',
            'subject' => 'required|string',
            'university_id' => 'required|exists:universities,id',
        ];
    }
    public function messages()
    {
        return [
            'university_id.exists' => 'معرف الجامعة غير صحيح.'
        ];
    }
}
