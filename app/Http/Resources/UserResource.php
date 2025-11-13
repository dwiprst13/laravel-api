<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'name' => $this->name,
            'role' => $this->role,
            'avatar_url' => $this->avatar_path ? Storage::disk('public')->url($this->avatar_path) : null,
        ];
        if ($request->user()?->isAdmin()) {
            $data['id'] = $this->id;
            $data['email'] = $this->email;
            $data['created_at'] = optional($this->created_at)?->toIso8601String();
            $data['updated_at'] = optional($this->updated_at)?->toIso8601String();
        }
        return $data;
    }
}
