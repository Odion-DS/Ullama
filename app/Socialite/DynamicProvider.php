<?php

namespace App\Socialite;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class DynamicProvider extends AbstractProvider
{
    protected $scopes = ['openid', 'profile', 'email'];
    protected $scopeSeparator = ' ';
    protected $guzzle = [];

    public function __construct($request, $clientId, $clientSecret, $redirectUrl, $guzzle = [])
    {
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $guzzle);
        $this->guzzle = $guzzle;
    }

    protected function getHttpClient()
    {
        if (is_null($this->httpClient)) {
            $options = array_merge(['http_errors' => false], $this->guzzle);

            $this->httpClient = new Client($options);
        }

        return $this->httpClient;
    }

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            $this->getConfig('authorize_url') ?? $this->getConfig('base_url') . '/application/o/authorize/',
            $state
        );
    }

    protected function getTokenUrl()
    {
        return $this->getConfig('token_url') ?? $this->getConfig('base_url') . '/application/o/token/';
    }

    protected function getUserByToken($token)
    {
        $userUrl = $this->getConfig('userinfo_url') ?? $this->getConfig('base_url') . '/application/o/userinfo/';

        $response = $this->getHttpClient()->get($userUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['sub'] ?? $user['id'] ?? null,
            'nickname' => $user['preferred_username'] ?? $user['username'] ?? null,
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $user['picture'] ?? null,
        ]);
    }

    protected function getConfig($key)
    {
        return config("services.{$this->getProviderName()}.{$key}");
    }

    protected function getProviderName()
    {
        return array_key_first(
            array_filter(config('services'), fn($v, $k) => is_array($v) && isset($v['enabled']), ARRAY_FILTER_USE_BOTH)
        );
    }
}
