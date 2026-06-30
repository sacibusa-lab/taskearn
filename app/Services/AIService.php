<?php

namespace App\Services;

use App\Models\AdminSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://openrouter.ai/api/v1';

    public function __construct()
    {
        $this->apiKey = AdminSetting::getValue('openrouter_api_key', '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Generate article content from a topic/title.
     */
    public function generateArticle(string $topic, string $style = 'educational'): array
    {
        $prompt = <<<PROMPT
You are a professional content writer. Write a high-quality, well-structured article about the following topic.

Topic: {$topic}

Style: {$style}

Requirements:
- Write at least 500 words
- Use clear headings (but don't use markdown heading syntax, just plain text with newlines)
- Make it engaging and informative
- Include real-world examples where applicable
- Write in simple, clear English suitable for a general audience
- Use short paragraphs (2-4 sentences each)
- Separate sections with blank lines

Return ONLY the article content, no introduction or explanation.
PROMPT;

        return $this->chat($prompt);
    }

    /**
     * Generate quiz questions from article content.
     */
    public function generateQuestions(string $content, int $count = 4): array
    {
        $prompt = <<<PROMPT
Based on the following article content, generate {$count} multiple-choice quiz questions to test reading comprehension.

Article Content:
{$content}

Return a valid JSON array (no markdown, no code fences) where each item has this exact structure:
{
  "question": "The question text?",
  "option_a": "First option",
  "option_b": "Second option",
  "option_c": "Third option",
  "option_d": "Fourth option",
  "correct_answer": "a"
}

Rules:
- correct_answer must be exactly "a", "b", "c", or "d"
- Questions should test real understanding, not trivial details
- Make sure the correct answer is truly correct based on the article
- Distractors should be plausible but incorrect
- Generate exactly {$count} questions
PROMPT;

        return $this->chat($prompt, true);
    }

    /**
     * Send a chat completion request to OpenRouter.
     */
    protected function chat(string $prompt, bool $expectJson = false): mixed
    {
        if (!$this->isConfigured()) {
            return $expectJson ? [] : ['error' => 'OpenRouter API key not configured'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url', 'http://localhost'),
                'X-Title' => config('app.name', 'TaskEarn'),
            ])->timeout(60)->post($this->baseUrl . '/chat/completions', [
                'model' => 'google/gemini-2.0-flash-exp:free',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'max_tokens' => 4000,
                'temperature' => 0.7,
            ]);

            if (!$response->successful()) {
                Log::error('OpenRouter API error: ' . $response->body());
                return $expectJson ? [] : ['error' => 'AI service unavailable. Please try again.'];
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';

            if ($expectJson) {
                // Try to extract JSON from the response
                $content = trim($content);
                // Remove markdown code fences if present
                $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
                $content = preg_replace('/\s*```$/', '', $content);
                $decoded = json_decode($content, true);
                return is_array($decoded) ? $decoded : [];
            }

            return ['content' => $content];

        } catch (\Exception $e) {
            Log::error('OpenRouter request failed: ' . $e->getMessage());
            return $expectJson ? [] : ['error' => 'Connection to AI service failed.'];
        }
    }
}
