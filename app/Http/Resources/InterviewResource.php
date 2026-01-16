<?php

namespace App\Http\Resources;

use App\Constants\Constants;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**  
 * @OA\Schema(  
 *     schema="InterviewResource",  
 *     type="object",  
 *     @OA\Property(property="name", type="string", maxLength=255, example="Sample Name"),  
 *     @OA\Property(property="description", type="string", example="Sample Description"),  
 *     @OA\Property(property="date", type="string", format="date", example="2023-10-01"),  
 *     @OA\Property(property="place", type="string", example="Sample Place"),  
 *     @OA\Property(
 *         property="images",
 *         type="string",
 *         format="binary",
 *     ),  
 * )  
 */  
class InterviewResource extends JsonResource
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
        $array =  parent::toArray($request);
        $array['type'] = Constants::INTERVIEW_TYPES[$array['type']]['ar'];
        return $array;
    }
}
