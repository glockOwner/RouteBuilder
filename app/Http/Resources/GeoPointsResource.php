<?php

namespace App\Http\Resources;

use App\Models\Geoposition;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

class GeoPointsResource extends JsonResource
{

    /**
     * @var array $point
     */
    private $point;

    /**
     * @var string $address
     */
    private $address;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->resource = $resource;

        $this->point = ['latitude' => $resource->latitude, 'longitude' => $resource->longitude];
        $this->address = $resource->address;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'point' => $this->point,
            'address' => $this->address,
        ];
    }
}
