<?php

namespace App\Http\Requests\Api\Admin\Interview;

use App\Constants\Constants;
use Illuminate\Foundation\Http\FormRequest;

/**  
 * @OA\Schema(  
 *     schema="CreateInterviewRequest",  
 *     type="object",  
 *     required={  
 *         "name",  
 *         "description",  
 *         "date",  
 *         "place",  
 *         "images[0]", "is_special"  , "type",
 *     },  
 *     @OA\Property(property="name", type="string", maxLength=255, example="Sample Name"),  
 *     @OA\Property(property="description", type="string", example="Sample Description"),  
 *     @OA\Property(property="date", type="string", format="date", example="2023-10-01"),  
 *     @OA\Property(property="place", type="string", example="Sample Place"),  
 *     @OA\Property(property="is_special", enum={"0","1"}),
 *     @OA\Property(property="type", enum={"digital","physical"}),
 *     @OA\Property(property="requests_type", enum={"electronic_certificate","cartoon_certificate","both"}),
 *     @OA\Property(property="sort_order", type="integer", example="1"),
 *     @OA\Property(
 *         property="images[0]",
 *         type="string",
 *         format="binary",
 *     ),  
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date_format:Y-m-d H:i:s|after:now',
            'place' => 'required|string',
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:gif,webp,png,jpg,jpeg',
            'is_special' => 'required|boolean',
            'type' => 'required|in:digital,physical',
            'requests_type' => 'nullable|in:'. implode(',', Constants::AVAILABLE_INTERVIEW_REQUESTS_TYPES),
            'sort_order' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    // Ensure the combination of section_id and sort_order is unique
                    if (\DB::table('interviews')
                        ->where('sort_order', $value)
                        ->whereNull('deleted_at')
                        ->exists()) {
                        $fail(__('حقل الترتيب موجود مسبقاً.'));
                    }
                },
            ],

        ];
    }
    public function messages()
    {
        return [
            'sort_order.unique' => 'حقل الترتيب موجود مسبقا',

        ];
    }
}
