<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class VisitorResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */


    public function toArray($request)
    {

        return [
            'id' => (int)$this->id,
            'unique_id' => $this->unique_id,
            'fcm_token' => $this->fcm_token,
            'f_name' => $this->f_name,
            'phone' => $this->phone,
            'address' => $this->address,
            'country_id' => $this->country_id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
        ];
    }


}
