<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResourse extends JsonResource
{

    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'discription' => $this->discription,
            'price' => $this->price,
            'active' => $this->active,
            'category' => $this->category->name,
            'media' => $this->media,
        ];
    }
}
