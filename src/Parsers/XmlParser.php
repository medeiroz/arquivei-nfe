<?php


namespace MedeirosDev\Arquivei\Parsers;

use stdClass;

class XmlParser
{
    /** @var string */
    public $base64;

    /** @var string */
    public $accessKey;

    /** @var string */
    public $xml;

    /** @var stdClass */
    public $object;


    public function __construct(string $base64, string $accessKey)
    {
        $this->base64 = $base64;
        $this->accessKey = $accessKey;
        $this->xml = base64_decode($this->base64);
        $this->object = $this->parse($this->xml);
    }


    private function parse(String $xml): stdClass
    {
        $object = json_decode(
            json_encode(
                (array) simplexml_load_string($xml)
            )
        );

        if (empty($object->infNFe) === true) {
            $object = $object->NFe;
        }

        return $object;
    }


}
