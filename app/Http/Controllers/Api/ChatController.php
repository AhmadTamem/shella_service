<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\GetChatRequest;
use App\Http\Requests\StoreChatRequest;
use App\Models\Chat;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
     /**
     * Gets chats
     *
     * @param GetChatRequest $request
     * @return JsonResponse
     */
    public function index(GetChatRequest $request)
    {
        $data = $request->validated();

        $isPrivate = 1;
        if ($request->has('is_private')) {
            $isPrivate = (int)$data['is_private'];
        }

        $chats = Chat::where('is_private', $isPrivate)
            ->hasParticipant(Auth::user()->id)
            ->whereHas('messages')
            ->with('lastMessage.user', 'participants.user')
            ->latest('updated_at')
            ->get();
        return $this->success($chats);
    }


    /**
     * Stores a new chat
     *
     * @param StoreChatRequest $request
     * @return JsonResponse
     */
    public function store(StoreChatRequest $request) 
    {
        try {
            $data = $this->prepareStoreData($request);
    
            if ($data['userId'] === $data['otherUserId']) {
                return $this->error('You cannot create a chat with yourself.');
            }
    
            $previousChat = $this->getPreviousChat($data['otherUserId']);
    
            if ($previousChat === null) {
                $chat = Chat::create($data['data']);
                $chat->participants()->createMany([
                    ['user_id' => $data['userId']],
                    ['user_id' => $data['otherUserId']],
                ]);
    
                $chat->refresh()->load('lastMessage.user', 'participants.user');
                return $this->success($chat);
            }
    
            return $this->success($previousChat->load('lastMessage.user', 'participants.user'));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Check if user and other user has previous chat or not
     *
     * @param int $otherUserId
     * @return mixed
     */
    private function getPreviousChat(int $otherUserId) : mixed {

        $userId =Auth::user()->id;

        return Chat::where('is_private',1)
            ->whereHas('participants', function ($query) use ($userId){
                $query->where('user_id',$userId);
            })
            ->whereHas('participants', function ($query) use ($otherUserId){
                $query->where('user_id',$otherUserId);
            })
            ->first();
    }


    /**
     * Prepares data for store a chat
     *
     * @param StoreChatRequest $request
     * @return array
     */
    private function prepareStoreData(StoreChatRequest $request) : array
    {
        $data = $request->validated();
        $otherUserId = (int)$data['user_id'];
        unset($data['user_id']);
        $data['created_by'] = Auth::user()->id;

        return [
            'otherUserId' => $otherUserId,
            'userId' => Auth::user()->id,
            'data' => $data,
        ];
    }


    /**
     * Gets a single chat
     *
     * @param Chat $chat
     * @return JsonResponse
     */
    public function show(Chat $chat)
    {
        $chat->load('lastMessage.user', 'participants.user');
        return $this->success($chat);
    }
}
