<?php


namespace MedeirosDev\Arquivei\Stores;


use MedeirosDev\Arquivei\Parsers\XmlParser;

class ListStore
{
    private $nfes = [];
    private $store;

    public function __construct(StoreInterface $store, XmlParser ...$nfes)
    {
        $this->store = $store;
        $this->nfes = $nfes;
    }

    public function store(): bool
    {
        foreach ($this->nfes as $nfe) {
            $this->store->store($nfe);
        }

        return true;
    }

}
