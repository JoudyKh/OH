<?php

namespace App\Http\Requests\Api\App\StudentProject;

use Illuminate\Foundation\Http\FormRequest;

/**  
 * @OA\Schema(  
 *     schema="CreateStuProRequest",  
 *     type="object",  
 *     required={  
 *         "full_name",  
 *         "year",  
 *         "subject",  
 *         "phone_number",  "university_id",
 *     },  
 *     @OA\Property(property="full_name", type="string", maxLength=255, example="Sample Name"),  
 *     @OA\Property(property="year", type="string", example="5"),  
 *     @OA\Property(property="subject", type="string", format="date", example="2023-10-01"),  
 *     @OA\Property(property="phone_number", type="string", example="Sample Place"),  
 *     @OA\Property(property="university_id", type="integer", example="1"),  
 *     @OA\Property(property="files", type="string", example="Sample Place"),  
 *     @OA\Property(
 *         property="files[0]",
 *         type="string",
 *         format="binary",
 *     ),  
 * )  
 */  
class CreateStuProRequest extends FormRequest
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
            'full_name' => 'required|string',
            'year' => 'required|string',
            'subject' => 'required|string',
            'phone_number' => 'required|string',
            'university_id' => 'required|exists:universities,id',
            'files' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'university_id.exists' => 'معرف الجامعة غير صحيح.'
        ];
    }
}
