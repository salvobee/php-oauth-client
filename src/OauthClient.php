<?php

namespace Blabs\OauthClient;

use Blabs\OauthClient\Contracts\AuthTokenStore;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class OauthClient
{
    private ClientInterface $httpClient;

    #[Pure] public function __construct(private ?AuthTokenStore $tokenStore = null)
    {
        $this->tokenStore = empty($tokenStore) ? new DefaultTokenStore() : $tokenStore;
    }

    /**
     * @throws Exceptions\AuthTokenMissingException
     */
    #[ArrayShape(['Authorization' => "string"])] public function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->getStoredToken()
        ];
    }

    /**
     * @throws Exceptions\AuthTokenMissingException
     */
    public function getStoredToken(): string
    {
        return $this->tokenStore->getStoredToken();
    }

    public function storeToken(string $token): self
    {
        $this->tokenStore->storeToken($token);
        return $this;
    }

    #[ArrayShape(['grant_type' => "string", 'client_id' => "string", 'client_secret' => "string", 'scope' => "string"])]
    public function getOauthTokenRequestAttributes(): array
    {
        return [
            'grant_type' => 'client_credentials',
            'client_id' => 'client-id',
            'client_secret' => 'client-secret',
            'scope' => '*',
        ];
    }

    public function setHttpClient(ClientInterface $httpClient): self
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    public function storeRefreshToken(string $randomString)
    {

    }

    public function getStoredRefreshToken()
    {
        return $this->tokenStore->getStoredRefreshToken();
    }
}