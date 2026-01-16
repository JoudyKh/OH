<?php

namespace App\Http\Requests\Api\Admin\StudentProject;

use App\Constants\Constants;
use Illuminate\Foundation\Http\FormRequest;

/**  
 * @OA\Schema(  
 *     schema="UpdateStuProStatusRequest",  
 *     type="object",  
 *     required={  
 *         "status",  
 *     },  
 *     @OA\Property(property="status", enum={"pending", "completed"}),  
 *     @OA\Property(property="mark", type="string", example="mark"),   
 *     @OA\Property(property="note", type="string", example="mark"),   
 * )  
 */  
class UpdateStuProStatusRequest extends FormRequest
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
                'status' => 'in:'. implode(',', Constants::PROJECT_STATUSES),  
                'mark' => 'required_if:status,' . Constants::PROJECT_STATUSES[1],
                'note' => 'nullable|string',
            ];
    }
}
