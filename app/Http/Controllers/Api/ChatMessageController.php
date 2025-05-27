<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\GetMessageRequest;
use App\Http\Requests\StoreMessageRequest;

use App\Models\ChatMessage;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatMessageController extends Controller
{
      /**
     * Gets chat message
     *
     * @param GetMessageRequest $request
     * @return JsonResponse
     */
    public function index(GetMessageRequest $request): JsonResponse
    {
        $data = $request->validated();
        $chatId = $data['chat_id'];
        $currentPage = $data['page'];
        $pageSize = $data['page_size'] ?? 15;

        $messages = ChatMessage::where('chat_id', $chatId)
            ->with('user')
            ->latest('created_at')
            ->simplePaginate(
                $pageSize,
                ['*'],
                'page',
                $currentPage
            );

        return $this->success($messages->getCollection());
    }

    /**
     * Create a chat message
     *
     * @param StoreMessageRequest $request
     * @return JsonResponse
     */
    public function store(StoreMessageRequest $request) : JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::user()->id;

        $chatMessage = ChatMessage::create($data);
        $chatMessage->load('user');



        return $this->success($chatMessage,'Message has been sent successfully.');
    }

 
}
