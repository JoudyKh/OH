<?php

namespace App\Http\Requests\Api\Admin\HomeSlider;

use Illuminate\Foundation\Http\FormRequest;

/**  
 * @OA\Schema(  
 *     schema="UpdateHomeSliderRequest",  
 *     type="object",  
 *     title="File Upload Request",  
 *     description="Request body for uploading a file",  
 *     @OA\Property(  
 *         property="image",  
 *         type="string",  
 *         format="binary",  
 *         description="The file to be uploaded. Accepts image and video formats: jpeg, jpg, png, gif, bmp, tiff, svg.",  
 *         nullable=false  
 *     ),
 *     @OA\Property(property="name", type="string", maxLength=255, example="Sample Name"),  
 *     @OA\Property(property="phone_number", type="string", maxLength=255, example="Sample Name"),  
 *     @OA\Property(property="city", type="string", maxLength=255, example="Sample Name"),    
 * )  
 */
class UpdateHomeSliderRequest extends FormRequest
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
            'image' => 'file|mimes:'.implode(',',['gif' ,'webp', 'jpeg','jpg','png','gif','bmp','tiff','svg']),
            'name' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'city' => 'nullable|string',
        ];
    }
}
