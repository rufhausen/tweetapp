<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class TwitterApi
{
    const MAXRESULT_COUNT = 3200;

    /**
     * @return Client
     */
    public function client(): Client
    {
        $stack = HandlerStack::create();

        $middleware = new Oauth1([
            'consumer_key'    => env('TWITTER_CONSUMER_KEY'),
            'consumer_secret' => env('TWITTER_CONSUMER_SECRET'),
            'token'           => env('TWITTER_ACCESS_TOKEN'),
            'token_secret'    => env('TWITTER_ACCESS_TOKEN_SECRET'),
        ]);
        $stack->push($middleware);

        $client = new Client([
            'base_uri' => 'https://api.twitter.com/1.1/',
            'handler'  => $stack,
            'auth'     => 'oauth',
            'http_errors' => false
        ]);

        return $client;
    }

    /**
     * @param string $uri
     * @param array|null $params
     * @return bool|mixed
     */
    public function get(string $uri, array $params = null)
    {
        if ($params) {
            $params = $this->setParams($params);
        }

        $uri = $uri . '.json';

        $response = $this->client()->get($uri . $params);

        if ($response->getStatusCode() == '200') {
            return json_decode($response->getBody()->getContents(), true);
        }

        return ['error' => $response->getReasonPhrase()];
    }

    /**
     * @param array $params
     * @return string
     */
    private function setParams(array $params): string
    {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $params[$key] = implode(',', $value);
            }
        }

        return '?' . str_replace('[0]', '', urldecode(http_build_query($params)));
    }

    /**
     * @return array
     */
    public function getRateLimitStatus(): array
    {
        return $this->get('application/rate_limit_status');
    }

    /**
     * @param string $statusId
     * @return array
     */
    public function getStatusById(string $statusId): array
    {
        return $this->get('statuses/show', ['id' => $statusId]);
    }

    /**
     * @return array
     */
    public function verifyCredentials(): array
    {
        return $this->get('account/verify_credentials');
    }

    /**
     * @param string $screenName
     * @param int $count
     * @return array
     */
    public function getUserTimeLine(string $screenName, int $count = self::MAXRESULT_COUNT): array
    {
        try {
            $response = $this->get('statuses/user_timeline', ['screen_name' => $screenName, 'count' => $count]);
        } catch (ClientException $e) {
            $response['error'] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @param string $screenName
     * @return array
     */
    public function getUserByScreenName(string $screenName): array
    {
        return $this->get('users/show', ['screen_name' => $screenName]);
    }
}
