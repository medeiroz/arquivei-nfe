<?php

namespace MedeirosDev\Arquivei\Tests\Integration;

use Dotenv\Dotenv;
use MedeirosDev\Arquivei\Arquivei;
use MedeirosDev\Arquivei\Parsers\XmlParser;
use MedeirosDev\Arquivei\Response\ArquiveiResponse;
use MedeirosDev\Arquivei\Stores\StoreInterface;
use PHPUnit\Framework\TestCase;
use Exception;
use Mockery;

class ArquiveiTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        (new Dotenv(__DIR__ . '/../../'))->load();

    }

    public function testRequest()
    {
        $arquivei = new Arquivei;

        $cursor = 0;

        $response = $arquivei->request($cursor);

        $this->assertInstanceOf(ArquiveiResponse::class, $response);
        $this->assertTrue($response->successful());
        $this->assertFalse($response->error());
        $this->assertIsArray($response->data);
        $this->assertEquals($response->count, count($response->data));
        $this->assertGreaterThan(0, $response->count);
        $this->assertGreaterThan($cursor, $response->nextCursor);
    }


    public function testRequestAll()
    {
        $arquivei = new Arquivei;

        $responses = $arquivei->requestAll();

        $this->assertIsArray($responses);

        foreach($responses as $key => $response) {

            $this->assertTrue($response->successful());
            $this->assertFalse($response->error());
            $this->assertIsArray($response->data);
            $this->assertGreaterThan(0, $response->count);
            $this->assertEquals($response->count, count($response->data));

            if ($key === 0) {
                $this->assertGreaterThan(0, $response->nextCursor);
            } else {
                $this->assertGreaterThan($responses[$key - 1]->nextCursor, $response->nextCursor);
            }
        }

    }

    public function testParse()
    {
        $arquivei = new Arquivei;
        $response = $arquivei->request(0);
        $listParsed= $arquivei->parse($response);

        $this->assertInstanceOf(ArquiveiResponse::class, $response);
        $this->assertTrue($response->successful());
        $this->assertIsArray($listParsed);

        $this->assertContainsOnlyInstancesOf(XmlParser::class, $listParsed);
    }

    public function testStore()
    {
        $mockStore = Mockery::mock(StoreInterface::class)
            ->shouldReceive('store')
            ->andReturn(true)
            ->mock();

        $arquivei = new Arquivei($mockStore);
        $response = $arquivei->request(0);
        $listParsed= $arquivei->parse($response);

        $storeResponse = $arquivei->store(...$listParsed);

        $this->assertTrue($storeResponse);
    }

    public function testStoreException()
    {
        $mockStore = Mockery::mock(StoreInterface::class)
            ->shouldReceive('store')
            ->andThrow(new Exception)
            ->mock();

        $arquivei = new Arquivei($mockStore);
        $response = $arquivei->request(0);
        $listParsed= $arquivei->parse($response);

        $this->expectException(Exception::class);

        $arquivei->store(...$listParsed);
    }
}
