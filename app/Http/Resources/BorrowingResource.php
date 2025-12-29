<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BorrowingResource extends JsonResource
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
            'book_id' => $this->book_id,
            'member_id' => $this->member_id,
            'borrowed_date' => $this->borrowed_date ? $this->borrowed_date->format('Y-m-d') : null,
            'due_date' => $this->due_date ?  $this->due_date->format('Y-m-d') : null,
            'status' => $this->status,
            'returned_date' => $this->returned_date ? $this->returned_date->format('Y-m-d') : null,
            'member' => new MemberResource($this->whenLoaded('member')),
            'book' => new BookResource($this->whenLoaded('book'))
        ];
    }
}
