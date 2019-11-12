<?php

namespace MedeirosDev\Arquivei\Response;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Url\Url;

class ArquiveiResponse
{
    protected $response;
    protected $code;
    protected $json;
    public $data;
    public $count;
    public $previousCursor;
    public $nextCursor;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;

        $this->code = (int) $response->getStatusCode();
        $this->json = json_decode($response->getBody()->getContents());
        $this->data = $this->json->data;
        $this->count = $this->json->count;
        $this->previousCursor = Url::fromString($this->json->page->previous)->getQueryParameter('cursor');
        $this->nextCursor = Url::fromString($this->json->page->next)->getQueryParameter('cursor');

    }

    public function successful(): bool
    {
        return $this->code === Response::HTTP_OK;
    }

    public function error(): bool
    {
        return $this->code !== Response::HTTP_OK;
    }






}
