<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**  
 * @OA\Schema(  
 *     schema="InterviewRequestResource",  
 *     type="object",  
 *     @OA\Property(property="name", type="string", example="Sample Name"),  
 *     @OA\Property(property="academic_achievement", type="string", example="Sample Name"),  
 *     @OA\Property(property="university", type="string", example="Sample Name"),  
 *     @OA\Property(property="phone_number", type="string", example="Sample Name"),  
 *     @OA\Property(property="first_name", type="string", example="Sample Name"),  
 *     @OA\Property(property="father_name", type="string", example="Sample Name"),  
 *     @OA\Property(property="last_name", type="string", example="Sample Name"),  
 *     @OA\Property(property="mother_name", type="string", example="Sample Name"),  
 *     @OA\Property(property="birth_place_and_date", type="string", example="Sample Name"),  
 *     @OA\Property(property="national_id", type="string", example="Sample Name"),  
 *     @OA\Property(property="registration_place", type="string", example="Sample Name"),  
 *     @OA\Property(property="central_secretariat", type="string", example="Sample Name"),  
 *     @OA\Property(property="gender", enum={"male","female"}),  
 *     @OA\Property(property="face_color", type="string", example="Sample Name"),  
 *     @OA\Property(property="eyes_color", type="string", example="Sample Name"),  
 *     @OA\Property(property="address", type="string", example="Sample Name"),  
 *     @OA\Property(property="email", type="email", example="email@email.com"),  
 *     @OA\Property(property="delivery_address", type="string", example="Sample Name"),  
 * )  
 */ 
class InterviewRequestResource extends JsonResource
{
     /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed> | \Illuminate\Pagination\AbstractPaginator| \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function collection($data)
    {
        /*
        This simply checks if the given data is and instance of Laravel's paginator classes
         and if it is,
        it just modifies the underlying collection and returns the same paginator instance
        */
        if (is_a($data, \Illuminate\Pagination\AbstractPaginator::class)) {
            $data->setCollection(
                $data->getCollection()->map(function ($listing) {
                    return new static($listing);
                })
            );
            return $data;
        }

        return parent::collection($data);
    }
    public function toArray(Request $request): array
    {
        $array = parent::toArray($request);
        if($this->university_id)
            $array['university'] = UniversityResource::make($this->university);
        return $array;
    }
}
