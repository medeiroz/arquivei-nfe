<?php


namespace MedeirosDev\Arquivei\Stores;


use MedeirosDev\Arquivei\Parsers\XmlParser;

interface StoreInterface
{
    public function store(XmlParser $nfe): bool;
}