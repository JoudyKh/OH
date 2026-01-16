<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**  
 * @OA\Schema(  
 *     schema="StudentProjectResource",  
 *     type="object",  
 *     @OA\Property(property="full_name", type="string", maxLength=255, example="Sample Name"),  
 *     @OA\Property(property="year", type="string", example="Sample Description"),  
 *     @OA\Property(property="subject", type="string", format="date", example="2023-10-01"),  
 *     @OA\Property(property="phone_number", type="string", example="Sample Place"),  
 *     @OA\Property(
 *         property="files",
 *         type="string",
 *         format="binary",
 *     ),  
 * )  
 */  
class StudentProjectResource extends JsonResource
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
        $array['university'] = UniversityResource::make($this->university);
        return $array;
    }
}
