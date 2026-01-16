<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="SectionResource",
 *     type="object",
 *     title="Section",
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="image", type="string", example="exampleImageUrl"),
 * )
 */
class SectionResource extends JsonResource
{

    
    /**
     * Transform the resource into an array.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Pagination\AbstractPaginator
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
    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $array = [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            'description' => $this->description,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        if($this->type === 'sub_section'){
            $array['super_section'] = SectionResource::make($this->superSection);
            $array['sub_type'] = $this->sub_type;
        }elseif($this->type === 'sub_library_section'){
            $array['is_special'] = $this->is_special;
            // $array['super_library_section'] = SectionResource::make($this->superLibrarySec);
        }elseif($this->type === 'super_library_section'){
            $array['sub_libraries_sec'] = SingleSectionResource::collection($this->subLibrariesSec);
        }

        return $array;
    }
}
