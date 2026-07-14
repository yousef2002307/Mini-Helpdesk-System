<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReplyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'body'       => $this->body,
            'author'     => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
                'role' => $this->user->role,
            ],
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
