<?php

namespace App\Http\Requests\Api\Admin\Student;

use Illuminate\Foundation\Http\FormRequest;
/**  
 * @OA\Schema(  
 *     schema="UpdateStudentRequest",  
 *     type="object",  
 *     @OA\Property(property="name", type="string", example="CS101"),  
 *     @OA\Property(property="university_number", type="string", example="1"),  
 *     @OA\Property(property="phone_number", type="string", example="1548845"),  
 *     @OA\Property(property="password", type="string", example="password"),  
 *     @OA\Property(property="study_year", type="string", example="3"),  
 *     @OA\Property(property="university_id", type="integer", example="3"),  
 * )  
 */
class UpdateStudentRequest extends FormRequest
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
            'name' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'password' => 'nullable|min:8',
            'study_year' => 'nullable|string',
            'university_number' => 'nullable|string|unique:users,university_number,' . $this->route('student')->id,
            'university_id' => 'nullable|exists:universities,id',
        ];
    }
}
