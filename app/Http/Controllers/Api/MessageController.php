<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MessageController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $messages = Message::query()
            ->latest('created_at')
            ->paginate((int) min($request->integer('per_page', 15) ?: 15, 100))
            ->withQueryString();

        return MessageResource::collection($messages);
    }

    public function store(StoreMessageRequest $request): JsonResponse
    {
        $message = Message::create($request->validated());

        return MessageResource::make($message)
            ->response()
            ->setStatusCode(201);
    }

    public function show(Message $message): MessageResource
    {
        return MessageResource::make($message);
    }

    public function destroy(Message $message): JsonResponse
    {
        $message->delete();

        return response()->json([
            'message' => 'Message deleted.',
        ]);
    }
}
