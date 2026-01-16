<?php
namespace App\Http\Requests\Api\Admin\Section;

use App\Constants\Constants;
use App\Traits\HandlesValidationErrorsTrait;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Section;

class StoreSectionRequest extends FormRequest
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
     * Prepare the data for validation.
     */
    public function prepareForValidation()
    {
        $this->merge([
            'type' => $this->route('type') ? trim($this->route('type')) : 'super_section',
            'parentSection' => $this->route('parentSection'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return array_merge(
            Constants::SECTIONS_TYPES[$this->type]['rules']['create'] ?? [],
            [
                'type' => 'required|in:' . implode(',', array_keys(Constants::SECTIONS_TYPES)),
                'sub_type' => [
                    'required_if:type,sub_section',
                    'in:' . implode(',', array_keys(Constants::SUB_SECTION_TYPES)),
                ],
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|max:2048',
                'sort_order' => 'required|integer',
            ]
        );
    }

    /**
     * Add custom validation logic after basic validation rules.
     *
     * @param \Illuminate\Validation\Validator $validator
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $parentSectionId = $this->route('parentSection') ? $this->route('parentSection')->id : null;
            $type = $this->route('type');

            if ($parentSectionId) {
                $parentSection = Section::find($parentSectionId);

                // Validate parentSection type
                if ($parentSection && !in_array($parentSection->type, Constants::PARENTS)) {
                    $validator->errors()->add('parentSection', __('الرقم المعرف للقسم غير صحيح'));
                }

                // Validate compatibility between parentSection and child type
                if (
                    $parentSection &&
                    (!array_key_exists($parentSection->type, Constants::CHILDREN_OF) ||
                        !in_array($type, Constants::CHILDREN_OF[$parentSection->type]))
                ) {
                    $validator->errors()->add('type', __('خطأ في نوع القسم'));
                }
            }

            // Validate uniqueness for sort_order
            if (
                Section::where('type', $type)
                    ->where('sort_order', $this->input('sort_order'))
                    ->when($this->input('sub_type'), function ($query) {
                        $query->where('sub_type', $this->input('sub_type'));
                    })
                    ->when($this->input('parentSection'), function ($query) {
                        $query->where('parent_id', $this->input('parentSection')->id);
                    })
                    ->whereNull('deleted_at')
                    ->exists()
            ) {
                $validator->errors()->add('sort_order', __('حقل الترتيب موجود مسبقا'));
            }
        });
    }

    /**
     * Custom error messages.
     */
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
}
