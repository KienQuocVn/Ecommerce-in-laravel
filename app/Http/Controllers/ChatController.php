<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY = 2000000; // 2 seconds in microseconds
    private const TIMEOUT = 120; // 2 minutes

    public function chat(Request $req)
    {
        $apiKey = env('GROQ_API_KEY', '');

        if (empty($apiKey)) {
            return response()->json([
                'content' => 'Error: GROQ_API_KEY not configured',
                'raw' => null
            ], 500);
        }

        $model = $req->input('model', 'meta-llama/llama-4-scout-17b-16e-instruct');
        $messages = $req->input('messages', []);

        // Validate messages
        if (empty($messages)) {
            return response()->json([
                'content' => 'Error: No messages provided',
                'raw' => null
            ], 400);
        }

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => (float)($req->input('temperature', 0.5)),
            'stream' => false,
            'max_tokens' => 2048, // Limit response length
        ];

        Log::info('Chat request', [
            'model' => $model,
            'message_count' => count($messages),
        ]);

        // Retry logic
        $lastError = null;
        for ($attempt = 0; $attempt < self::MAX_RETRIES; $attempt++) {
            if ($attempt > 0) {
                Log::info("Retry attempt {$attempt} after error");
                usleep(self::RETRY_DELAY * $attempt); // Exponential backoff
            }

            try {
                $result = $this->callGroqAPI($apiKey, $payload);

                if ($result['success']) {
                    return response()->json([
                        'content' => $result['content'],
                        'raw' => $result['raw'],
                    ]);
                }

                $lastError = $result['error'];

                // Don't retry on client errors (4xx)
                if ($result['status'] >= 400 && $result['status'] < 500) {
                    break;
                }
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                Log::error('Exception in chat attempt', [
                    'attempt' => $attempt,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // All retries failed
        Log::error('All chat attempts failed', ['last_error' => $lastError]);
        return response()->json([
            'content' => 'Service temporarily unavailable. Please try again. Error: ' . ($lastError ?? 'Unknown error'),
            'raw' => null
        ], 503);
    }

    private function callGroqAPI(string $apiKey, array $payload): array
    {
        $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . $apiKey,
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 90,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $out = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'success' => false,
                'error' => "Network error: {$curlError}",
                'status' => 0
            ];
        }

        if ($httpCode !== 200) {
            $json = json_decode($out ?: '{}', true);
            $errorMsg = $json['error']['message'] ?? "HTTP {$httpCode}";

            Log::error('Groq API error', [
                'status' => $httpCode,
                'error' => $errorMsg,
                'response' => substr($out, 0, 500)
            ]);

            return [
                'success' => false,
                'error' => $errorMsg,
                'status' => $httpCode
            ];
        }

        $json = json_decode($out ?: '{}', true);
        $content = $json['choices'][0]['message']['content'] ?? null;

        if (!$content) {
            return [
                'success' => false,
                'error' => 'No content in response',
                'status' => $httpCode
            ];
        }

        return [
            'success' => true,
            'content' => $content,
            'raw' => $json,
            'status' => $httpCode
        ];
    }
}
