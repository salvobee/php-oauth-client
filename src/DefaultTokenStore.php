<?php

namespace Blabs\OauthClient;

use Blabs\OauthClient\Contracts\AuthTokenStore;
use Blabs\OauthClient\Exceptions\AuthTokenMissingException;

class DefaultTokenStore implements AuthTokenStore
{

    public function __construct(public ?string $storedToken = null, private ?string $storedRefreshToken = null)
    {
    }


    /**
     * @throws AuthTokenMissingException
     */
    public function getStoredToken(): ?string
    {
        if (empty($this->storedToken))
            throw new AuthTokenMissingException();

        return $this->storedToken;
    }

    public function storeToken(string $token)
    {
        $this->storedToken = $token;
    }

    public function getStoredRefreshToken()
    {
       return $this->storedRefreshToken;
    }

    public function storeRefreshToken(string $token)
    {
        $this->storedRefreshToken = $token;
    }
}