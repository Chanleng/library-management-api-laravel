<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'address' => $this->address,
            'membership_date' => $this->membership_date ? $this->membership_date->format('Y-m-d') : null,
            'status' => $this->status,
            'borrowed_count' => $this->when($this->relationLoaded('activeBorrowings'), $this->activeBorrowings->count())
        ];
    }
}
