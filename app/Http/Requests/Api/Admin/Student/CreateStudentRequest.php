<?php

namespace App\Http\Requests\Api\Admin\Student;

use Illuminate\Foundation\Http\FormRequest;

/**  
 * @OA\Schema(  
 *     schema="CreateStudentRequest",  
 *     type="object",  
 *     required={  
 *         "name", "university_number", "password", "phone_number", "study_year", "university_id", 
 * },  
 *     @OA\Property(property="name", type="string", example="CS101"),  
 *     @OA\Property(property="university_number", type="string", example="1"),  
 *     @OA\Property(property="phone_number", type="string", example="1548845"),  
 *     @OA\Property(property="password", type="string", example="password"),  
 *     @OA\Property(property="study_year", type="string", example="3"),  
 *     @OA\Property(property="university_id", type="integer", example="3"),  
 * )  
 */
class CreateStudentRequest extends FormRequest
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
            'password' => 'required|min:8',
            'study_year' => 'required|string',
            'university_number' => 'required|string|unique:users,university_number',
            'university_id' => 'required|exists:universities,id',
        ];
    }
}
