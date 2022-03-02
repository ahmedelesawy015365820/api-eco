<?php

namespace App\Http\Resources;

use App\Traits\Message;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResourse extends JsonResource
{

    use Message;

    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "active" => $this->active,
            'product' => $this->product_count,
            'media' => $this->media->file_name
        ];

    }
}