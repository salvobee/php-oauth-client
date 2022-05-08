<?php

namespace Blabs\OauthClient\Tests;

use Blabs\OauthClient\Exceptions\AuthTokenMissingException;
use Blabs\OauthClient\OauthClient;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class OauthClientTest extends TestCase
{
    protected OauthClient $client;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = new OauthClient();
    }

    /**
     * @test
     */
    public function it_can_generate_auth_token_header_attributes()
    {
        $headers = $this->client->storeToken('anyTokensYouLike')->getHeaders();

        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertEquals(1, preg_match('/^(?i)Bearer (.*)(?-i)/',$headers['Authorization']));
    }

    /**
     * @test
     */
    public function it_will_throw_an_exception_when_auth_token_is_missing()
    {
        $this->expectException(AuthTokenMissingException::class);
        $this->client->getToken();
    }

    /**
     * @test
     */
    public function it_can_store_and_read_auth_token()
    {
        $randomString = $this->generateRandomString(32);
        $this->client->storeToken($randomString);
        $this->assertEquals($randomString, $this->client->getStoredToken());
    }

    /**
     * @test
     */
    public function it_can_store_and_read_refresh_token()
    {
        $randomString = $this->generateRandomString(32);
        $this->client->storeRefreshToken($randomString);
        $this->assertEquals($randomString, $this->client->getStoredRefreshToken());
    }

    /**
     * @test
     */
    public function it_can_generate_oauth_token_request_attributes()
    {
        $oauthTokenRequestAttributes = $this->client->getOauthTokenRequestAttributes();
        $this->assertArrayHasKey('grant_type',$oauthTokenRequestAttributes);
        $this->assertEquals('client_credentials',$oauthTokenRequestAttributes['grant_type']);
        $this->assertArrayHasKey('scope', $oauthTokenRequestAttributes);
        $this->assertEquals('*', $oauthTokenRequestAttributes['scope']);
        $this->assertArrayHasKey('client_id',$oauthTokenRequestAttributes);
        $this->assertArrayHasKey('client_secret',$oauthTokenRequestAttributes);
    }

    /**
     * @test
     */
    public function it_can_perform_the_request_to_get_auth_token_using_an_oauth_server_mock()
    {
        $randomGeneratedToken = $this->generateRandomString();
        $randomGeneratedRefreshToken = $this->generateRandomString(24);

        $oauthTokenMock = new MockHandler([
            new Response(200, [], "{
                token: $randomGeneratedToken,
                refresh_token: $randomGeneratedRefreshToken
            }")
        ]);
        $handlerStack = HandlerStack::create($oauthTokenMock);
        $client = new Client(['handler' => $handlerStack]);

        $this->assertEquals($randomGeneratedToken, $this->client->setHttpClient($client)->getToken());
    }

    /**
     * @param int $length
     * @return string
     */
    public function generateRandomString(int $length = 32): string
    {
        return substr(md5(rand()), 0, $length);
    }
}