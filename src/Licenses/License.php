<?php

namespace MedeirosDev\Arquivei\Licenses;

/**
 * Class License
 *
 * @package MedeirosDev\Arquivei\Licenses
 */
class License
{
    /**
     * License Api id
     *
     * @var string
     */
    private $api_id;

    /**
     * License Api key
     *
     * @var string
     */
    private $api_key;

    /**
     * License constructor.
     * @param string $api_id
     * @param string $api_key
     */
    public function __construct(string $api_id, string $api_key)
    {
        $this->api_id = $api_id;
        $this->api_key = $api_key;
    }

    /**
     * @return array
     */
    public function getHeadersParameters(): array
    {
        return [
            'x-api-id' => $this->api_id,
            'x-api-key' => $this->api_key,
        ];
    }
}
