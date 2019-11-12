<?php

namespace MedeirosDev\Arquivei;

use MedeirosDev\Arquivei\Exceptions\ServiceTemporarilyUnavailableException;
use MedeirosDev\Arquivei\Licenses\License;
use MedeirosDev\Arquivei\Parsers\XmlParser;
use MedeirosDev\Arquivei\Request\ArquiveiRequest;
use MedeirosDev\Arquivei\Response\ArquiveiResponse;
use MedeirosDev\Arquivei\Parsers\Parser;
use MedeirosDev\Arquivei\Stores\StoreInterface;
use MedeirosDev\Arquivei\Stores\ListStore;
use Exception;

class Arquivei
{

    /** @var License */
    protected $license;

    /** @var StoreInterface  */
    protected $store;

    /**  @var int  */
    public $limit = 50;

    public function __construct(?StoreInterface $store = null)
    {
        $this->store = $store;

        $this->setLicense(
            new License(
                Helper::config('arquivei.api_id'),
                Helper::config('arquivei.api_key')
            )
        );
    }

    public function request(int $cursor = 0): ArquiveiResponse
    {
        try {
            return (new ArquiveiRequest($this))->request($cursor);
        } catch (ServiceTemporarilyUnavailableException $exception) {
            sleep(10);
            return $this->request();
        }
    }

    public function requestAll(): array
    {
        $responses = [];
        $cursor = 0;

        do {
            $responses[] = $response = $this->request($cursor);
            $cursor = $response->nextCursor;

        } while($response->count >= $this->limit);

        return $responses;
    }

    public function requestAllAndStore(): array
    {
        $responses = $this->requestAll();

        foreach ($responses as $response) {
            $parsed = $this->parse($response);
            $this->store(...$parsed);
        }

        return $responses;
    }

    public function store(XmlParser ...$parsed): bool
    {
        $store = new ListStore($this->store, ...$parsed);
        return $store->store();
    }

    public function parse(ArquiveiResponse $response): array
    {
        return (new Parser($response))->parse();
    }

    public function getLicense(): ?License
    {
        return $this->license;
    }

    public function setLicense(License $license): self
    {
        $this->license = $license;
        return $this;
    }

    public function getStore(): ?StoreInterface
    {
        return $this->store;
    }

    public function setStore(StoreInterface $store): self
    {
        $this->store = $store;
        return $this;
    }


}
