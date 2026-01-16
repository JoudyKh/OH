<?php
namespace App\Http\Requests\Api\Admin\Section;

use App\Constants\Constants;
use App\Traits\HandlesValidationErrorsTrait;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Section;

// class UpdateSectionRequest extends FormRequest
// {
//     use HandlesValidationErrorsTrait;

//     /**
//      * Determine if the user is authorized to make this request.
//      */
//     public function authorize(): bool
//     {
//         return true;
//     }

//     /**
//      * Prepare the data for validation.
//      */
//     public function prepareForValidation()
//     {
//         $this->merge([
//             'type' => $this->route('section')->type,
//         ]);
//     }

//     /**
//      * Get the validation rules that apply to the request.
//      *
//      * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
//      */
//     public function rules()
//     {
//         return array_merge(
//             Constants::SECTIONS_TYPES[$this->input('type')]['rules']['update'] ?? [],
//             [
//                 'type' => 'required|in:' . implode(',', array_keys(Constants::SECTIONS_TYPES)),
//                 'sub_type' => [
//                     'required_if:type,sub_section',
//                     'in:' . implode(',', array_keys(Constants::SUB_SECTION_TYPES)),
//                 ],
//                 'name' => 'nullable|string|max:255',
//                 'image' => 'nullable|image|max:2048',
//                 'sort_order' => [
//                     'nullable',
//                     'integer',
//                     function ($attribute, $value, $fail) {
//                         $sectionId = $this->route('section')->id;

//                         // Check for uniqueness of sort_order, excluding the current section
//                         $query = Section::where('type', $this->input('type'))
//                             ->where('sort_order', $value)
//                             ->where('id', '!=', $sectionId);

//                         if ($this->input('sub_type')) {
//                             $query->where('sub_type', $this->input('sub_type'));
//                         }

//                         if ($query->exists()) {
//                             $fail(__('حقل الترتيب موجود مسبقًا.'));
//                         }
//                     },
//                 ],
//             ]
//         );
//     }

//     /**
//      * Custom error messages.
//      */
//     public function messages()
//     {
//         return [
//             'sub_type.required_if' => 'نوع القسم مطلوب.',
//             'type.required' => 'نوع القسم مطلوب.',
//             'type.in' => 'نوع القسم غير صحيح.',
//             'name.required' => 'اسم القسم مطلوب.',
//             'name.max' => 'اسم القسم يجب أن لا يتجاوز 255 حرفًا.',
//             'image.image' => 'يجب أن تكون الصورة ملفًا من نوع صورة.',
//             'image.max' => 'الصورة يجب أن لا تزيد عن 2 ميغابايت.',
//             'sort_order.required' => 'ترتيب القسم مطلوب.',
//             'sort_order.integer' => 'ترتيب القسم يجب أن يكون عددًا صحيحًا.',
//         ];
//     }

//     /**
//      * Add custom validation logic after basic validation rules.
//      *
//      * @param \Illuminate\Validation\Validator $validator
//      */
//     public function withValidator($validator)
//     {
//         $validator->after(function ($validator) {
//             $parentSectionId = $this->route('parentSection') ? $this->route('parentSection')->id : null;

//             if ($parentSectionId) {
//                 $parentSection = Section::find($parentSectionId);

//                 // Validate parentSection type
//                 if ($parentSection && !in_array($parentSection->type, Constants::PARENTS)) {
//                     $validator->errors()->add('parentSection', __('الرقم المعرف للقسم غير صحيح'));
//                 }

//                 // Validate compatibility between parentSection and child type
//                 $type = $this->input('type');
//                 if ($parentSection &&
//                     (!array_key_exists($parentSection->type, Constants::CHILDREN_OF) || 
//                      !in_array($type, Constants::CHILDREN_OF[$parentSection->type]))) {
//                     $validator->errors()->add('type', __('خطأ في نوع القسم'));
//                 }
//             }
//         });
//     }
// }

class UpdateSectionRequest extends FormRequest
{
    use HandlesValidationErrorsTrait;

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
    public function rules()
    {
        return array_merge(
            Constants::SECTIONS_TYPES[$this->route('section')->type]['rules']['update'],
            [
                'sort_order' => [
                    'nullable',
                    'integer',
                    function ($attribute, $value, $fail) {
                        $section = $this->route('section');

                        // Check for uniqueness of sort_order, excluding the current section
                        $query = Section::where('type', $section->type)
                            ->where('sort_order', $value)
                            ->where('id', '!=', $section)
                            ->whereNull('deleted_at');

                        if ($section->sub_type) {
                            $query->where('sub_type', $section->sub_type);
                        }
                        if ($section->parent_id) {
                            $query->where('parent_id', $section->parent_id);
                        }

                        if ($query->exists()) {
                            $fail(__('حقل الترتيب موجود مسبقًا.'));
                        }
                    },
                ]
            ]
        );
    }
    public function messages()
    {
        return [
            'sub_type.required_if' => 'نوع القسم مطلوب.',
            'type.required' => 'نوع القسم مطلوب.',
            'type.in' => 'نوع القسم غير صحيح.',
            'name.required' => 'اسم القسم مطلوب.',
            'name.max' => 'اسم القسم يجب أن لا يتجاوز 255 حرفًا.',
            'image.image' => 'يجب أن تكون الصورة ملفًا من نوع صورة.',
            'image.max' => 'الصورة يجب أن لا تزيد عن 2 ميغابايت.',
            'sort_order.required' => 'ترتيب القسم مطلوب.',
            'sort_order.integer' => 'ترتيب القسم يجب أن يكون عددًا صحيحًا.',
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $parentSectionId = $this->route('parentSection') ? $this->route('parentSection')->id : null;

            if ($parentSectionId) {
                $parentSection = Section::find($parentSectionId);

                // Validate parentSection type
                if ($parentSection && !in_array($parentSection->type, Constants::PARENTS)) {
                    $validator->errors()->add('parentSection', __('الرقم المعرف للقسم غير صحيح'));
                }

                // Validate compatibility between parentSection and child type
                $type = $this->input('type');
                if (
                    $parentSection &&
                    (!array_key_exists($parentSection->type, Constants::CHILDREN_OF) ||
                        !in_array($type, Constants::CHILDREN_OF[$parentSection->type]))
                ) {
                    $validator->errors()->add('type', __('خطأ في نوع القسم'));
                }
            }
        });
    }
}