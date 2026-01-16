<?php

namespace App\Http\Requests\Api\Admin\Interview;

use App\Constants\Constants;
use Illuminate\Foundation\Http\FormRequest;

/**  
 * @OA\Schema(  
 *     schema="UpdateInterviewRequest",  
 *     type="object",  
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
 *     @OA\Property(property="delete_images[0]"),  
 * )  
 */  
class UpdateInterviewRequest extends FormRequest
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
            'description' => 'nullable|string',
            'date' => 'nullable|date_format:Y-m-d H:i:s|after:now',
            'place' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:gif,webp,png,jpg,jpeg',
            'delete_images' => 'nullable|exists:interview_images,id',
            'is_special' => 'sometimes|boolean',
            'type' => 'sometimes|in:digital,physical',
            'requests_type' => 'nullable|in:'. implode(',', Constants::AVAILABLE_INTERVIEW_REQUESTS_TYPES),
            'sort_order' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    // Ensure the combination of section_id and sort_order is unique
                    if (\DB::table('interviews')
                        ->where('sort_order', $value)
                        ->where('id', '!=', $this->route('interview')->id)
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
