<?php

namespace App\Service\Realtime;

use App\Exception\Realtime\CentrifugoApiException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CentrifugoClient
{
    private const float TIMEOUT_SECONDS = 2.0;

    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire(env: 'CENTRIFUGO_API_URL')]
        private string $apiUrl,
        #[Autowire(env: 'CENTRIFUGO_API_KEY')]
        private string $apiKey,
    ) {
    }

    public function hasSubscribers(string $channel): bool
    {
        $result = $this->call('presence_stats', ['channel' => $channel]);

        return ($result['num_clients'] ?? 0) > 0;
    }

    public function publish(string $channel, array $data): void
    {
        $this->call('publish', ['channel' => $channel, 'data' => $data]);
    }

    private function call(string $method, array $params): array
    {
        $response = $this->httpClient->request('POST', rtrim($this->apiUrl, '/') . '/' . $method, [
            'headers' => ['X-API-Key' => $this->apiKey],
            'json' => $params,
            'timeout' => self::TIMEOUT_SECONDS,
        ]);

        $payload = $response->toArray();

        if (isset($payload['error'])) {
            throw new CentrifugoApiException(
                $payload['error']['message'] ?? 'Unknown Centrifugo error',
                $payload['error']['code'] ?? 0,
            );
        }

        return $payload['result'] ?? [];
    }
}
