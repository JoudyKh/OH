<?php

namespace App\Http\Requests\Api\Admin\LibraryFile;

use Illuminate\Foundation\Http\FormRequest;

/**  
 * @OA\Schema(  
 *     schema="UpdateLibraryFileRequest",  
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
 *     @OA\Property(property="sort_order", type="integer", example="1"),
 * )  
 */
class UpdateLibraryFileRequest extends FormRequest
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
        $file = $this->route('file');
        return [
            'name' => 'sometimes|string|max:255',
            'image' => 'sometimes|image|mimes:gif,webp,png,jpg,jpeg',
            'file' => 'sometimes|file',
            'sort_order' => [
                'integer',
                function ($attribute, $value, $fail)use($section, $file) {
                    // Ensure the combination of section_id and sort_order is unique
                    if (\DB::table('library_files')
                        ->where('sub_library_id', $section->id)
                        ->where('sort_order', $value)
                        ->where('id', '!=', $file->id)
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
