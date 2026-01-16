<?php

namespace App\Http\Requests\Api\Admin\Library;

use Illuminate\Foundation\Http\FormRequest;

/**  
 * @OA\Schema(  
 *     schema="UpdateLibraryRequest",  
 *     type="object",  
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
 *     @OA\Property(property="delete_images[0]"),  
 * )  
 */
class UpdateLibraryRequest extends FormRequest
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
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'is_special' => 'sometimes|boolean',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:gif,webp,png,jpg,jpeg',
            'delete_images' => 'nullable|exists:library_images,id',
            'sort_order' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) use ($section) {
                    // Check for uniqueness of sort_order in the same section
                    $libraryId = $this->route('library')->id;
                        if ($value &&
                            \DB::table('libraries')
                                ->where('section_id', $section->id)
                                ->where('sort_order', $value)
                                ->where('id', '!=', $libraryId)
                                ->whereNull('deleted_at')
                                ->exists()
                        ) {
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
