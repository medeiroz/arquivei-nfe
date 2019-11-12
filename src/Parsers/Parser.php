<?php

namespace MedeirosDev\Arquivei\Parsers;


use MedeirosDev\Arquivei\Response\ArquiveiResponse;

class Parser
{
    /** @var ArquiveiResponse */
    private $response;

    /** @var array */
    private $nfes = [];

    public function __construct(ArquiveiResponse $response)
    {
        $this->response = $response;
    }


    public function parse(): array
    {
        foreach ($this->response->data as $nfe) {
            $this->nfes[] = new XmlParser($nfe->xml, $nfe->access_key);
        }

        return $this->nfes;
    }


    public function getNfes(): array
    {
        return $this->nfes;
    }
}
