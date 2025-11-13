<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->when($this->shouldIncludeContent($request), $this->content),
            'excerpt' => $this->excerpt,
            'featured_image_url' => $this->featured_image ? Storage::disk('public')->url($this->featured_image) : null,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'featured_image_alt' => $this->featured_image_alt,
            'tags' => $this->tags ?? [],
            'status' => $this->status,
            'author' => UserResource::make($this->whenLoaded('author')),
            'likes_count' => $this->whenCounted('likes'),
            'comments_count' => $this->whenCounted('comments'),
            'created_at' => optional($this->created_at)?->toIso8601String(),
        ];

        if ($this->shouldIncludeDetailMeta($request)) {
            $data['category_slug'] = $this->category_slug;
        }

        if ($request->user()?->isAdmin()) {
            $data['user_id'] = $this->user_id;
            $data['updated_at'] = optional($this->updated_at)?->toIso8601String();
        }

        return $data;
    }

    protected function shouldIncludeContent(Request $request): bool
    {
        if (! $request->isMethod('get')) {
            return true;
        }

        return (bool) $request->route()?->parameter('post');
    }

    protected function shouldIncludeDetailMeta(Request $request): bool
    {
        if (! $request->isMethod('get')) {
            return true;
        }

        return (bool) $request->route()?->parameter('post');
    }
}
