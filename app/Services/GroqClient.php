<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqClient
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->baseUrl = config('services.groq.api_url', env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'));
        $this->apiKey = (string)config('services.groq.api_key', env('GROQ_API_KEY', ''));
        $this->model = (string)config('services.groq.model', env('GROQ_MODEL', 'openai/gpt-oss-120b'));
    }

    public function chat(array $messages, array $opts = []): array
    {
        $payload = array_filter([
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $opts['temperature'] ?? 0.2,
            'max_tokens' => $opts['max_tokens'] ?? 800,
            'top_p' => $opts['top_p'] ?? 1.0,
            'stream' => false,
        ]);

        try {
            $resp = Http::withToken($this->apiKey)
                ->timeout(90)
                ->baseUrl($this->baseUrl)
                ->asJson()
                ->post('/chat/completions', $payload)
                ->throw();

            $data = $resp->json();
            $content = Arr::get($data, 'choices.0.message.content', '');

            return [
                'content' => $content,
                'raw' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Groq API error', [
                'error' => $e->getMessage(),
                'model' => $this->model,
                'base_url' => $this->baseUrl,
            ]);
            throw $e;
        }
    }

    public function buildMessages(string $userText, array $productBullets): array
    {
        $system = <<<SYS
Bạn là AI Chat Support chuyên nghiệp cho trang thương mại điện tử.

Quy tắc quan trọng:
- Chỉ giới thiệu sản phẩm CÓ trong danh sách ngữ cảnh được cung cấp.
- Mỗi đề xuất sản phẩm PHẢI kèm "Link: <url>" đúng như trong context.
- Viết NGẮN GỌN, TIẾNG VIỆT tự nhiên, thân thiện.
- KHÔNG dùng Markdown, KHÔNG dùng ký tự in đậm/italic, KHÔNG dùng dấu *, #, hoặc code block.
- Dùng gạch đầu dòng bằng "- " đơn giản (không **, không __).
- Nếu không tìm thấy sản phẩm phù hợp, hãy gợi ý các từ khóa tìm kiếm khác hoặc danh mục sản phẩm.
- Luôn cung cấp thông tin về giá, size, tình trạng hàng nếu có trong context.
SYS;

        $context = "Context (sản phẩm khớp tìm kiếm):\n" . implode("\n", $productBullets);

        return [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $context . "\n\nYêu cầu của khách hàng: " . $userText],
        ];
    }
}
