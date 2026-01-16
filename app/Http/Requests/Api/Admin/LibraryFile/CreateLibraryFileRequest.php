<?php

namespace App\Http\Requests\Api\Admin\LibraryFile;

use Illuminate\Foundation\Http\FormRequest;

/**  
 * @OA\Schema(  
 *     schema="CreateLibraryFileRequest",  
 *     type="object",  
 *     @OA\Property(  
 *         property="name",  
 *         type="string",  
 *     ),
 *     @OA\Property(  
 *         property="image",  
 *         type="string",  
 *         format="binary",  
 *     ),
 *     @OA\Property(  
 *         property="file",  
 *         type="string",  
 *         format="binary",  
 *     ),
 *      @OA\Property(property="sort_order", type="integer", example="1"),
 * )  
 */
class CreateLibraryFileRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:gif,webp,png,jpg,jpeg',
            'file' => 'required|file',
            'sort_order' => [
                'required',
                'integer',
                function ($attribute, $value, $fail)use($section) {
                    // Ensure the combination of section_id and sort_order is unique
                    if (\DB::table('library_files')
                        ->where('sub_library_id', $section->id)
                        ->where('sort_order', $value)
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
