<?php

namespace MedeirosDev\Arquivei\Request;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use MedeirosDev\Arquivei\Exceptions\ServiceTemporarilyUnavailableException;
use MedeirosDev\Arquivei\Exceptions\UnauthorizedException;
use MedeirosDev\Arquivei\Helper;
use MedeirosDev\Arquivei\Arquivei;
use MedeirosDev\Arquivei\Response\ArquiveiResponse;
use MedeirosDev\Arquivei\Stack\LicenseMiddleware;

/**
 * Class ArquiveiRequest
 *
 * @package MedeirosDev\Arquivei
 */
class ArquiveiRequest
{
    protected $settings;
    protected $guzzleHttpClient;
    protected $handlerStack;

    /**
     * ArquiveiRequest constructor.
     *
     * @param Arquivei          $settings
     * @param MockHandler|CurlHandler|null $handler
     */
    public function __construct(Arquivei $settings, $handler = null)
    {
        $this->settings = $settings;
        $this->handlerStack = HandlerStack::create($handler ?? new CurlHandler());
        $this->guzzleHttpClient = new Client([
            'handler' => $this->handlerStack,
            'base_uri' => $this->getEndpoint(),
            'headers' =>  [
                'content-type' => 'application/json',
                'accept' => 'application/json',
            ],
        ]);

        $this->pushMiddleware(new LicenseMiddleware($this->settings->getLicense()));
    }

    /**
     * @param callable $middleware
     *
     * @return $this
     */
    public function pushMiddleware(callable $middleware)
    {
        $this->handlerStack->push($middleware);

        return $this;
    }

    /**
     * @param int $cursor
     * @return ArquiveiResponse
     * @throws ServiceTemporarilyUnavailableException
     * @throws UnauthorizedException
     */
    public function request(int $cursor = 0): ArquiveiResponse
    {
        try {

            return new ArquiveiResponse(
                $this->guzzleHttpClient->get(
                    $this->getVersion()
                    . '/nfe/received'
                    . "?cursor={$cursor}"
                    . "&limit={$this->settings->limit}"
                )
            );

        } catch (Exception $exception) {

            $body = json_decode($exception->getResponse()->getBody()->getContents());
            $code = $exception->getResponse()->getStatusCode();

            switch($code) {
                case 401:
                    throw new UnauthorizedException($body->error ?? 'Unauthorized');
                    break;
                case 502:
                case 503:
                    throw new ServiceTemporarilyUnavailableException('503 Service Temporarily Unavailable');
                default:
                    throw $exception;
            }
        }
    }

    private function getEndpoint()
    {
        return Helper::config('arquivei.endpoint');
    }

    private function getVersion()
    {
        return Helper::config('arquivei.version');
    }

}
