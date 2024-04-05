<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'profile_url' => $this->profile_url,
            'is_email_verified' => $this->is_email_verified,
            'is_admin' => true,
            'created_at' => $this->created_at,
        ];
    }
}
