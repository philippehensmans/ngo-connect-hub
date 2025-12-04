<?php

namespace App\Services;

/**
 * Service pour gérer les appels aux APIs d'IA externes
 * Supporte : Claude (Anthropic), OpenAI, Azure OpenAI
 */
class AIApiService
{
    private string $provider;
    private string $apiKey;
    private ?string $model;

    public function __construct(string $provider, string $apiKey, ?string $model = null)
    {
        $this->provider = $provider;
        $this->apiKey = $apiKey;
        $this->model = $model;
    }

    /**
     * Envoie un message à l'API et récupère la réponse
     *
     * @param array $messages Historique des messages [['role' => 'user', 'content' => '...']]
     * @param string $systemPrompt Prompt système pour contextualiser l'assistant
     * @return string Réponse de l'assistant
     */
    public function sendMessage(array $messages, string $systemPrompt): string
    {
        switch ($this->provider) {
            case 'claude':
                return $this->callClaudeApi($messages, $systemPrompt);

            case 'openai':
                return $this->callOpenAIApi($messages, $systemPrompt);

            case 'azure':
                return $this->callAzureApi($messages, $systemPrompt);

            default:
                throw new \RuntimeException("Unsupported AI provider: {$this->provider}");
        }
    }

    /**
     * Appel à l'API Claude (Anthropic)
     */
    private function callClaudeApi(array $messages, string $systemPrompt): string
    {
        $model = $this->model ?: 'claude-3-5-sonnet-20241022';

        $data = [
            'model' => $model,
            'max_tokens' => 1024,
            'system' => $systemPrompt,
            'messages' => $messages
        ];

        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-api-key: ' . $this->apiKey,
                'anthropic-version: 2023-06-01'
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \RuntimeException("Claude API error (HTTP {$httpCode}): {$response}");
        }

        $result = json_decode($response, true);

        if (!isset($result['content'][0]['text'])) {
            throw new \RuntimeException("Invalid Claude API response format");
        }

        return $result['content'][0]['text'];
    }

    /**
     * Appel à l'API OpenAI
     */
    private function callOpenAIApi(array $messages, string $systemPrompt): string
    {
        $model = $this->model ?: 'gpt-4';

        // Ajouter le système au début des messages pour OpenAI
        $allMessages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];
        $allMessages = array_merge($allMessages, $messages);

        $data = [
            'model' => $model,
            'messages' => $allMessages,
            'max_tokens' => 1024,
            'temperature' => 0.7
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \RuntimeException("OpenAI API error (HTTP {$httpCode}): {$response}");
        }

        $result = json_decode($response, true);

        if (!isset($result['choices'][0]['message']['content'])) {
            throw new \RuntimeException("Invalid OpenAI API response format");
        }

        return $result['choices'][0]['message']['content'];
    }

    /**
     * Appel à l'API Azure OpenAI
     */
    private function callAzureApi(array $messages, string $systemPrompt): string
    {
        // Note: L'URL Azure doit être configurée dans la clé API au format:
        // https://{resource-name}.openai.azure.com/openai/deployments/{deployment-id}/chat/completions?api-version=2023-05-15|{api-key}

        $parts = explode('|', $this->apiKey);
        if (count($parts) !== 2) {
            throw new \RuntimeException("Azure API key format: endpoint_url|api_key");
        }

        [$endpoint, $apiKey] = $parts;

        // Ajouter le système au début des messages
        $allMessages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];
        $allMessages = array_merge($allMessages, $messages);

        $data = [
            'messages' => $allMessages,
            'max_tokens' => 1024,
            'temperature' => 0.7
        ];

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'api-key: ' . $apiKey
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \RuntimeException("Azure API error (HTTP {$httpCode}): {$response}");
        }

        $result = json_decode($response, true);

        if (!isset($result['choices'][0]['message']['content'])) {
            throw new \RuntimeException("Invalid Azure API response format");
        }

        return $result['choices'][0]['message']['content'];
    }

    /**
     * Teste la connexion à l'API
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public function testConnection(): array
    {
        try {
            $testMessages = [
                ['role' => 'user', 'content' => 'Hello']
            ];

            $response = $this->sendMessage($testMessages, 'You are a helpful assistant. Respond with just "OK".');

            return [
                'success' => true,
                'message' => 'Connection successful',
                'response' => $response
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
