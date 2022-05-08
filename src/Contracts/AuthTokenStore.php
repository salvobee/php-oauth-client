<?php

namespace Blabs\OauthClient\Contracts;

use Blabs\OauthClient\Exceptions\AuthTokenMissingException;

interface AuthTokenStore
{
    /**
     * @throws AuthTokenMissingException
     */
    public function getStoredToken(): ?string;

    public function storeToken(string $token);

    public function getStoredRefreshToken();

    public function storeRefreshToken(string $token);
}