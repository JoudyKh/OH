<?php

namespace App\Http\Requests\Api\App\InterviewRequest;

use App\Constants\Constants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**  
 * @OA\Schema(  
 *     schema="CreateInterviewRequest2",  
 *     type="object",  
 *     @OA\Property(property="name", type="string", example="Sample Name"),  
 *     @OA\Property(property="academic_achievement", type="string", example="Sample Name"),  
 *     @OA\Property(property="university_id", type="integer", example="1"),  
 *     @OA\Property(property="phone_number", type="string", example="Sample Name"),  
 *     @OA\Property(property="first_name", type="string", example="Sample Name"),  
 *     @OA\Property(property="father_name", type="string", example="Sample Name"),  
 *     @OA\Property(property="last_name", type="string", example="Sample Name"),  
 *     @OA\Property(property="mother_name", type="string", example="Sample Name"),  
 *     @OA\Property(property="birth_place", type="string", example="Sample Name"),  
 *     @OA\Property(property="birth_date", type="string", format="date", example="2023-10-01"),  
 *     @OA\Property(property="national_id", type="string", example="Sample Name"),  
 *     @OA\Property(property="registration_place", type="string", example="Sample Name"),  
 *     @OA\Property(property="central_secretariat", type="string", example="Sample Name"),   
 *     @OA\Property(property="gender", enum={"male","female"}),  
 *     @OA\Property(property="address", type="string", example="Sample Name"),  
 *     @OA\Property(property="email", type="email", example="email@email.com"),  
 *     @OA\Property(property="delivery_address", type="string", example="Sample Name"),  
 * )  
 */
class CreateInterviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'type' => $this->route('type'),
        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $type = $this->route('type');
        $interview = $this->route('interview');
        $interviewType = $interview->type;
        $rules = [
            'type' => [
                'bail',
                'required',
                Rule::in(Constants::INTERVIEW_REQUESTS_TYPES),
            ],
            'phone_number' => 'string|required',
        ];
        $defaultRules = [
            'first_name' => 'string|required',
            'father_name' => 'string|required',
            'last_name' => 'string|required',
            'mother_name' => 'string|required',
            'birth_place' => 'string|required',
            'birth_date' => 'required|date_format:Y-m-d',
            'national_id' => 'string|required',
            'registration_place' => 'string|required',
            'central_secretariat' => 'string|required',
            'gender' => 'in:male,female|string|required',
            'email' => 'email|required',
            'address' => 'string|required',
        ];

        switch ($type) {
            case Constants::INTERVIEW_REQUESTS_TYPES[2]:
                $rules = array_merge($rules, [
                    'name' => 'string|required',
                    'academic_achievement' => 'string|required',
                    'university_id' => 'required|exists:universities,id',
                ]);
                if ($interviewType === 'digital') {
                    $rules['email'] = 'email|required'; 
                }
                break;

            case Constants::INTERVIEW_REQUESTS_TYPES[1]:
                $rules = array_merge($rules, [
                    'delivery_address' => 'string|required',
                ]);
                $rules = array_merge($rules, $defaultRules);
                break;

            default:
                $rules = array_merge($rules, $defaultRules);
                break;
        }

        return $rules;
    }
    public function messages()
    {
        return [
            'university_id.exists' => 'معرف الجامعة غير صحيح.'
        ];
    }
}
