<?php

namespace App\Http\Requests\Api\Admin\Library;

use Illuminate\Foundation\Http\FormRequest;

/**  
 * @OA\Schema(  
 *     schema="CreateLibraryRequest",  
 *     type="object",  
 *     required={  
 *         "name",  
 *         "description",  "sort_order",
 *         "phone_number", "address", "is_special",
 *     },  
 *     @OA\Property(property="name", type="string", maxLength=255, example="Sample Name"),  
 *     @OA\Property(property="description", type="string", example="Sample Description"),  
 *     @OA\Property(property="phone_number", type="string", example="+98641322"),  
 *     @OA\Property(property="address", type="string", example="address"),
 *     @OA\Property(property="sort_order", type="integer", example="1"),
 *     @OA\Property(property="is_special", enum={"0","1"}),
 *     @OA\Property(
 *         property="images[0]",
 *         type="string",
 *         format="binary",
 *     ),  
 * )  
 */
class CreateLibraryRequest extends FormRequest
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
        $section = $this->route('section');
        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'phone_number' => 'required|string',
            'address' => 'required|string',
            'is_special' => 'required|boolean',
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|mimes:gif,webp,png,jpg,jpeg',
            'sort_order' => [
                'required',
                'integer',
                function ($attribute, $value, $fail)use($section) {
                    // Ensure the combination of section_id and sort_order is unique
                    if (\DB::table('libraries')
                        ->where('section_id', $section->id)
                        ->where('sort_order', $value)
                        ->whereNull('deleted_at')
                        ->exists()) {
                        $fail(__('حقل الترتيب موجود مسبقاً.'));
                    }
                },
            ],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages()
    {
        return [
            'sort_order.unique' => 'حقل الترتيب موجود مسبقا',

        ];
    }
}
