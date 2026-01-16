<?php

namespace App\Http\Requests\Api\Admin\Lecture;

use App\Constants\Constants;
use App\Models\Lecture;
use Illuminate\Foundation\Http\FormRequest;

/**  
 * @OA\Schema(  
 *     schema="CreateLectureRequest",  
 *     type="object",  
 *     required={  
 *         "name",  
 *     },  
 *     @OA\Property(property="name", type="string", maxLength=255, example="Sample Name", description="The name of the section."),  
 *     @OA\Property(property="links[0][name]", type="string", example="Sample Name"),  
 *     @OA\Property(property="links[0][link]", type="string"),  
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
 *         property="paragraphs[0][image]",
 *         type="string",
 *         format="binary",
 *     ),  
 *     @OA\Property(
 *         property="paragraphs[0][description]",
 *         type="string",
 *     ),  
 * )  
 */
class CreateLectureRequest extends FormRequest
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
            'name' => 'required|string',
            'links' => 'array',
            'links.*.name' => 'required|string',
            'links.*.link' => 'required|string',
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|mimes:gif,webp,png,jpg,jpeg',
            'files' => 'array|min:1',
            'files.*.file' => 'required|file',
            'files.*.name' => 'required|string',
            'files.*.image' => 'required|image|mimes:gif,webp,png,jpg,jpeg',
            'paragraphs' => 'array|min:1',
            'paragraphs.*.image' => 'nullable|image|mimes:gif,webp,png,jpg,jpeg',
            'paragraphs.*.description' => 'nullable|string',
            'sort_order' => 'required|integer',

        ];
        $projectRules = [
            'requirements_image' => 'image|mimes:gif,webp,png,jpg,jpeg',
            'description' => 'string',
            'requirements' => 'string',
            'city' => 'nullable|string',
            'classification_id' => 'string|required_if:type,project|exists:classifications,id',
            'notes' => 'string',
        ];
        if ($type === 'project') {
            $rules = array_merge($rules, $projectRules);
        }

        return $rules;
    }
    public function withValidator($validator)
    {
        $type = $this->route('type');
        $section = $this->route('section');
        $validator->after(function ($validator) use($type, $section) {
            if (request()->has(['sort_order'])) {
                if (Lecture::where('type', $type)
                    ->where('sort_order', request('sort_order'))
                    ->where('sub_section_id', $section->id)
                    ->whereNull('deleted_at')
                    ->exists()) {
                    $validator->errors()->add('sort_order', 'حقل الترتيب موجود مسبقا');
                }
            }
        });
    }
    
    public function messages()
    {
        return [
            'type.in' => 'النوع المختار غير صحيح, أو نوع الأب المختار غير صحيح',
            'requirements_image.required_if' => 'الحقل مطلوب',  
            'description.required_if' => 'الحقل مطلوب',  
            'requirements.required_if' => 'الحقل مطلوب',  
            'classification.required_if' => 'الحقل مطلوب',   
            'classification.exists' => 'خطأ في معرف التصنيف',   
            'notes.required_if' => 'الحقل مطلوب',  
            'sort_order.unique' => 'حقل الترتيب موجود مسبقا',

        ];
    }
}
