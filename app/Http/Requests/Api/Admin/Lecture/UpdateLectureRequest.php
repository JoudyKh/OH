<?php

namespace App\Http\Requests\Api\Admin\Lecture;

use App\Constants\Constants;
use App\Models\Lecture;
use Illuminate\Foundation\Http\FormRequest;

/**  
 * @OA\Schema(  
 *     schema="UpdateLectureRequest",  
 *     type="object",  
 *     @OA\Property(property="name", type="string", maxLength=255, example="Sample Name", description="The name of the section."),  
 *     @OA\Property(property="links[0][name]", type="string", example="Sample Name"),  
 *     @OA\Property(property="links[0][link]", type="string", example="https://link@link",),  
 *     @OA\Property(property="delete_links[0]", type="integer", example="1"),  
 *     @OA\Property(property="update_links[0][id]", type="integer", example="1"),  
 *     @OA\Property(property="requirements_image", type="string", format="binary", description="An image file required if type is a specific value."),  
 *     @OA\Property(property="description", type="string", example="Detailed description of the section.", description="A detailed description required if type is a specific value."),  
 *     @OA\Property(property="requirements", type="string", example="List of requirements.", description="Requirements if applicable."),  
 *     @OA\Property(property="city", type="string", example="City Name", description="City associated with the section."),  
 *     @OA\Property(property="classification_id", type="integer"),  
 *     @OA\Property(property="sort_order", type="integer", example="1"),
 *     @OA\Property(property="notes", type="string", example="Any additional notes.", description="Optional notes related to the section."),
 *     @OA\Property(
 *         property="images[0]",
 *         type="string",
 *         format="binary",
 *     ),  
 *     @OA\Property(
 *         property="delete_images[0]",
 *         type="integer",
 *     ),  
 *     @OA\Property(
 *         property="files[0][name]",
 *         type="string",
 *     ),  
 *     @OA\Property(
 *         property="files[0][file]",
 *         type="string",
 *         format="binary",
 *     ),  
 *     @OA\Property(
 *         property="files[0][image]",
 *         type="string",
 *         format="binary",
 *     ),  
 *     @OA\Property(
 *         property="delete_files[0]",
 *         type="integer",
 *     ),  
 *     @OA\Property(
 *         property="update_files[0][id]",
 *         type="integer",
 *     ),  
 *     @OA\Property(
 *         property="paragraphs[0][image]",
 *         type="string",
 *         format="binary",
 *     ),  
 *     @OA\Property(
 *         property="paragraphs[0][description]",
 *         type="string",
 *     ),   
 *     @OA\Property(
 *         property="delete_paragraphs[0]",
 *         type="integer",
 *     ),   
 *     @OA\Property(
 *         property="update_paragraphs[0][id]",
 *         type="integer",
 *     ),   
 * )  
 */
class UpdateLectureRequest extends FormRequest
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
        $section = $this->route('section');
        $sub_type = $section->sub_type;
        $type = $this->route('type');
        $lecture = $this->route('lecture');
        $rules = [
            'type' => [
                'bail',
                'required',
                'in:' . implode(',', array_keys(Constants::SUB_SECTION_TYPES)),
                function ($attribute, $value, $fail) use ($sub_type) {
                    if ($value !== $sub_type) {
                        return $fail('The type must match the sub type.');
                    }
                },
            ],
            'name' => 'sometimes|string',

            'links' => 'array',
            'links.*.name' => 'required|string',
            'links.*.link' => 'required|string',

            'delete_links' => 'array',
            'delete_links.*' => 'required|exists:lecture_links,id,lecture_id,' . $lecture->id,

            'update_links' => 'array',
            'update_links.*.id' => 'required|exists:lecture_links,id,lecture_id,' . $lecture->id,
            'update_links.*.name' => 'sometimes|string',
            'update_links.*.link' => 'sometimes|string',

            'images' => 'array',
            'images.*' => 'required|image|mimes:gif,webp,png,jpg,jpeg',

            'delete_images' => 'array',
            'delete_images.*' => 'required|exists:lecture_images,id,lecture_id,' . $lecture->id,

            'files' => 'array',
            'files.*.file' => 'required|file',
            'files.*.name' => 'required|string',
            'files.*.image' => 'required|image|mimes:gif,webp,png,jpg,jpeg',

            'delete_files' => 'array',
            'delete_files.*' => 'required|exists:lecture_attached_files,id,lecture_id,' . $lecture->id,

            'update_files' => 'array',
            'update_files.*.id' => 'required|exists:lecture_attached_files,id,lecture_id,' . $lecture->id,
            'update_files.*.file' => 'sometimes|file',
            'update_files.*.name' => 'sometimes|string',
            'update_files.*.image' => 'sometimes|image|mimes:gif,webp,png,jpg,jpeg',

            'paragraphs' => 'array',
            'paragraphs.*.image' => 'nullable|image|mimes:gif,webp,png,jpg,jpeg',
            'paragraphs.*.description' => 'nullable|string',

            'delete_paragraphs' => 'array',
            'delete_paragraphs.*' => 'required|exists:lecture_paragraphs,id,lecture_id,' . $lecture->id,

            'update_paragraphs' => 'array',
            'update_paragraphs.*.id' => 'required|exists:lecture_paragraphs,id,lecture_id,' . $lecture->id,
            'update_paragraphs.*.image' => 'nullable|image|mimes:gif,webp,png,jpg,jpeg',
            'update_paragraphs.*.description' => 'nullable|string',

            'sort_order' => 'nullable|integer',

        ];
        $projectRules = [
            'requirements_image' => 'nullable|image|mimes:gif,webp,png,jpg,jpeg',
            'description' => 'string',
            'requirements' => 'string',
            'city' => 'nullable|string',
            'classification_id' => 'string|exists:classifications,id',
            'notes' => 'nullable|string',
        ];
        if ($type === 'project') {
            $rules = array_merge($rules, $projectRules);
        }

        return $rules;
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (request()->has(['sort_order'])) {
                $lecture = $this->route('lecture'); // Retrieve the ID from the route
                $type = $this->route('type');
                $section = $this->route('section');

                if (
                    Lecture::where('type', $type)
                        ->where('sort_order', request('sort_order'))
                        ->where('id', '!=', $lecture->id) // Exclude the current record
                        ->where('sub_section_id', $section->id)
                        ->whereNull('deleted_at')
                        ->exists()
                ) {
                    $validator->errors()->add('sort_order', 'حقل الترتيب موجود مسبقا');
                }
            }
        });
    }

}
