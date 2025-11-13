<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatSendRequest;
use App\Http\Resources\ProductResource;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Services\GroqClient;
use App\Services\ProductSearchService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ChatController extends Controller
{
    public function send(ChatSendRequest $request, ProductSearchService $search, GroqClient $groq)
    {
        $user = $request->user();
        $text = trim($request->input('text', ''));
        $chatId = $request->input('chat_id');

        // Get or create chat (allow guest users with user_id = null)
        $chat = $chatId
            ? Chat::where('user_id', $user?->id)->findOrFail($chatId)
            : Chat::create(['user_id' => $user?->id, 'title' => null]);

        // Handle greetings
        if ($this->isGreeting($text)) {
            $reply = "Chào bạn! Mình có thể giúp bạn tìm sản phẩm theo:\n- Tên sản phẩm (ví dụ: áo vest, quần jeans)\n- Thương hiệu/nhãn hiệu\n- Danh mục\n- Tầm giá (ví dụ: dưới 500k, từ 200-500k)\n- Size (S, M, L, XL...)\n- Tình trạng hàng (còn hàng, hết hàng)\n\n";

            DB::transaction(function () use ($chat, $text, $reply) {
                ChatMessage::create([
                    'chat_id' => $chat->id,
                    'role' => 'user',
                    'content' => $text,
                    'meta' => null,
                ]);
                ChatMessage::create([
                    'chat_id' => $chat->id,
                    'role' => 'assistant',
                    'content' => $reply,
                    'meta' => ['products' => []],
                ]);
            });

            return response()->json([
                'chat_id' => $chat->id,
                'reply' => $reply,
                'matched_products' => [],
            ]);
        }

        // Search products
        $filters = $search->guessFiltersFromText($text);
        try {
            $products = $search->search(array_merge(['q' => $text], $filters), 8);
        } catch (QueryException $qe) {
            Log::warning('Product search failed, fallback to LIKE', ['err' => $qe->getMessage()]);
            $products = $search->search(array_merge(['q' => $text], $filters), 8);
        }

        $bullets = $search->toPromptBullets($products);

        $reply = null;
        $aiRaw = null;

        try {
            $messages = $groq->buildMessages($text, $bullets);
            $ai = $groq->chat($messages, [
                'temperature' => 0.2,
                'max_tokens' => 700,
            ]);
            $reply = (string) $ai['content'];
            $aiRaw = $ai['raw'] ?? null;
        } catch (Throwable $e) {
            Log::error('Groq call failed', ['err' => $e->getMessage()]);
            if ($products->count()) {
                $lines = $products->map(function ($p) {
                    $brandName = $p->brand ? $p->brand->title : 'N/A';
                    $finalPrice = $p->discount ? ($p->price * (1 - $p->discount / 100)) : $p->price;
                    $priceStr = number_format($finalPrice, 0, ',', '.');
                    return "- {$p->title} ({$brandName}) — {$priceStr} VNĐ — Link: " . route('product-detail', $p->slug);
                })->implode("\n");
                $reply = "Mình chưa gọi được AI, nhưng có vài sản phẩm phù hợp:\n{$lines}";
            } else {
                $reply = "Mình chưa gọi được AI và hiện chưa tìm thấy sản phẩm phù hợp. Bạn thử mô tả lại (tên sản phẩm, thương hiệu, danh mục, tầm giá)…";
            }
        }

        DB::transaction(function () use ($chat, $text, $reply, $products, $filters, $aiRaw) {
            ChatMessage::create([
                'chat_id' => $chat->id,
                'role' => 'user',
                'content' => $text,
                'meta' => ['filters' => $filters],
            ]);

            ChatMessage::create([
                'chat_id' => $chat->id,
                'role' => 'assistant',
                'content' => $reply,
                'meta' => [
                    'products' => $products->take(8)->values()->toArray(),
                    'ai_raw' => $aiRaw,
                ],
            ]);
        });

        if (app()->isLocal()) {
            Log::debug('SEARCH_DEBUG', [
                'text' => $text,
                'filters' => $filters,
                'count' => $products->count(),
                'ids' => $products->pluck('id'),
            ]);
        }

        return response()->json([
            'chat_id' => $chat->id,
            'reply' => $reply,
            'matched_products' => ProductResource::collection($products),
        ]);
    }

    protected function isGreeting(string $text): bool
    {
        $t = mb_strtolower(trim($text));
        $greetings = [
            'hi',
            'hello',
            'hey',
            'chào',
            'xin chào',
            'chao',
            'alo',
            'yo',
            'hola',
            'sup',
            'hi!',
            'hello!',
            'chào bạn',
            'chào ad',
            'chào shop',
            'good morning',
            'good afternoon',
            'good evening'
        ];

        foreach ($greetings as $g) {
            if ($t === $g || str_starts_with($t, $g . ' ')) {
                return true;
            }
        }

        // Check if it's a very short message without product keywords
        if (str_word_count($t) <= 3 && !preg_match('/\d+|áo|vest|quần|giày|túi|sản phẩm|hàng|giá|size|m|l|xl|s/i', $t)) {
            return true;
        }

        return false;
    }
}
