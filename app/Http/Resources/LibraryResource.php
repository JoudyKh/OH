<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**  
 * @OA\Schema(  
 *     schema="LibraryResource",  
 *     type="object",  
 *     @OA\Property(property="name", type="string", maxLength=255, example="Sample Name"),  
 *     @OA\Property(property="description", type="string", example="Sample Description"),  
 *     @OA\Property(property="phone_number", type="string", example="+98641322"),  
 *     @OA\Property(property="address", type="string", example="address"),
 *     @OA\Property(
 *         property="image",
 *         type="string",
 *         format="binary",
 *         description="if the type is super_section the Image file nullable"
 *     ),  
 * )  
 */
class LibraryResource extends JsonResource
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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'section' => SectionResource::make($this->librarySec),
            'images' => $this->images,
            'sort_order' => $this->sort_order,
            'is_special' => $this->is_special,
        ];
    }
}
