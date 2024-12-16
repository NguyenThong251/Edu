<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatBot;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ChatBotController extends Controller
{
    // Đặt API URL và API Key của Gemini
    protected $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';
    protected $geminiApiKey;

    public function __construct()
    {
        $this->geminiApiKey = env('GEMINI_API_KEY'); // Lấy API key từ file .env
    }

    // Lưu cuộc trò chuyện và gọi Gemini API
    public function saveChat(Request $request)
    {
        // Lấy user hiện tại đang đăng nhập
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Bạn chưa đăng nhập'], 401);
        }
        $validator = Validator::make($request->all(), [
            'chat_bot_message' => 'required|string|max:500',
        ], [
            'chat_bot_message.required' => __('messages.chat_bot_messages_required'),
            'chat_bot_message.string' => __('messages.chat_bot_messages_string'),
            'chat_bot_message.max' => __('messages.chat_bot_messages_max'),
        ]);
    
        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), __('messages.validation_error'));
        }
        
        // Tin nhắn của người dùng
        $userMessage = $request->input('chat_bot_message');
        
        // Gọi Gemini API
        $geminiResponse = $this->callGeminiApi($userMessage);

        if (!$geminiResponse) {
            return response()->json([
                'error' => 'Không kết nối được với Gemini API',
            ], 500);
        }

        $responseMessage = $geminiResponse['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';        
        // Lưu cuộc trò chuyện vào bảng chat_bots
        $chat = ChatBot::firstOrCreate(
            ['user_id' => $user->id], // Điều kiện tìm kiếm
            ['conversation' => json_encode([])] // Dữ liệu mặc định nếu không tìm thấy
        );
    
        // Lấy cuộc trò chuyện hiện tại và giải mã JSON
        $conversation = json_decode($chat->conversation, true);
    
        // Nối thêm đoạn hội thoại mới
        $conversation[] = ['role' => 'user', 'content' => $userMessage];
        $conversation[] = ['role' => 'assistant', 'content' => $responseMessage];
    
        // Cập nhật lại trường `conversation`
        $chat->update(['conversation' => json_encode($conversation)]);

        return response()->json([
            'message' => 'Cuộc trò chuyện đã được lưu!',
            'response' => $responseMessage,
        ], 201);
    }


    // Lấy lịch sử trò chuyện của user
    public function getChatHistory()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Bạn chưa đăng nhập'], 401);
        }
        $chat = ChatBot::where('user_id', auth()->user()->id)->first();
        if (!$chat) {
            return response()->json(['message' => 'Chưa có lịch sử trò chuyện'], 404);
        }
        $chat->conversation=json_decode($chat->conversation);
        
        return response()->json(json_decode($chat));
    }

    // Hàm gọi API Gemini
    private function callGeminiApi($conversationText)
    {
        $payload = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $conversationText],
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->geminiApiUrl}?key={$this->geminiApiKey}", $payload);

            if ($response->status() !== 200) {
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            return null;
        }
    }
}
