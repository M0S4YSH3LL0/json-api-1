<?php

namespace CloudCreativity\JsonApi\Http\Client;

use CloudCreativity\JsonApi\Encoder\Encoder;
use CloudCreativity\JsonApi\Object\ResourceIdentifier;
use CloudCreativity\JsonApi\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Neomerx\JsonApi\Contracts\Schema\SchemaProviderInterface;
use Neomerx\JsonApi\Encoder\Parameters\EncodingParameters;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use function GuzzleHttp\Psr7\parse_query;

class GuzzleClientTest extends TestCase
{

    /**
     * @var Mock
     */
    private $encoder;

    /**
     * @var object
     */
    private $record;

    /**
     * @var MockHandler
     */
    private $mock;

    /**
     * @var GuzzleClient
     */
    private $client;

    /**
     * @return void
     */
    protected function setUp()
    {
        /** @var Encoder $encoder */
        $encoder = $this->encoder = $this->createMock(Encoder::class);
        $this->record = (object) [
            'type' => 'posts',
            'id' => '1',
            'attributes' => ['title' => 'Hello World'],
        ];

        $schema = $this->createMock(SchemaProviderInterface::class);
        $schema->method('getResourceType')->willReturn('posts');
        $schema->method('getId')->with($this->record)->willReturn('1');

        $container = $this->createMock(ContainerInterface::class);
        $container->method('getSchema')->with($this->record)->willReturn($schema);

        /** @var ContainerInterface $container */
        $this->client = new GuzzleClient(new Client([
            'handler' => $this->mock = new MockHandler(),
            'base_uri' => 'http://localhost/api/v1/',
        ]), $container, $encoder);
    }

    public function testCreateWithoutId()
    {
        $this->record->id = null;
        $this->willSerializeRecord()->willSeeRecord(201);
        $response = $this->client->create($this->record);

        $this->assertSame(201, $response->getPsrResponse()->getStatusCode());
        $this->assertRequested('POST', '/posts');
        $this->assertRequestSentRecord();
        $this->assertHeader('Accept', 'application/vnd.api+json');
        $this->assertHeader('Content-Type', 'application/vnd.api+json');
    }

    public function testCreateWithClientGeneratedId()
    {
        $this->willSerializeRecord()->willSeeRecord(201);
        $this->client->create($this->record);
        $this->assertRequested('POST', '/posts');
    }

    public function testCreateWithParameters()
    {
        $parameters = new EncodingParameters(
            ['author', 'site'],
            ['author' => ['first-name', 'surname'], 'site' => ['uri']]
        );

        $this->willSerializeRecord()->willSeeRecord(201);
        $this->client->create($this->record, $parameters);

        $this->assertQueryParameters([
            'include' => 'author,site',
            'fields[author]' => 'first-name,surname',
            'fields[site]' => 'uri',
        ]);
    }

    public function testCreateWithNoContentResponse()
    {
        $this->willSerializeRecord()->appendResponse(204);
        $response = $this->client->create($this->record);
        $this->assertSame(204, $response->getPsrResponse()->getStatusCode());
    }

    public function testRead()
    {
        $identifier = ResourceIdentifier::create('posts', '1');
        $this->willSeeRecord();
        $response = $this->client->read($identifier);

        $this->assertSame(200, $response->getPsrResponse()->getStatusCode());
        $this->assertRequested('GET', '/posts/1');
        $this->assertHeader('Accept', 'application/vnd.api+json');
    }

    public function testReadWithParameters()
    {
        $identifier = ResourceIdentifier::create('posts', '1');
        $parameters = new EncodingParameters(
            ['author', 'site'],
            ['author' => ['first-name', 'surname'], 'site' => ['uri']]
        );

        $this->willSeeRecord();
        $this->client->read($identifier, $parameters);

        $this->assertQueryParameters([
            'include' => 'author,site',
            'fields[author]' => 'first-name,surname',
            'fields[site]' => 'uri',
        ]);
    }

    public function testUpdate()
    {
        $this->willSerializeRecord()->willSeeRecord();
        $response = $this->client->update($this->record);

        $this->assertSame(200, $response->getPsrResponse()->getStatusCode());
        $this->assertRequested('PATCH', '/posts/1');
        $this->assertRequestSentRecord();
        $this->assertHeader('Accept', 'application/vnd.api+json');
        $this->assertHeader('Content-Type', 'application/vnd.api+json');
    }

    public function testUpdateWithFieldsets()
    {
        $expected = new EncodingParameters(
            null,
            ['posts' => $fields = ['content', 'published-at']]
        );

        $this->willSerializeRecord($expected)->willSeeRecord();
        $this->client->update($this->record, $fields);
    }

    public function testUpdateWithParameters()
    {
        $parameters = new EncodingParameters(
            ['author', 'site'],
            ['author' => ['first-name', 'surname'], 'site' => ['uri']]
        );

        $this->willSerializeRecord()->willSeeRecord(201);
        $this->client->update($this->record, [], $parameters);

        $this->assertQueryParameters([
            'include' => 'author,site',
            'fields[author]' => 'first-name,surname',
            'fields[site]' => 'uri',
        ]);
    }

    public function testUpdateWithNoContentResponse()
    {
        $this->willSerializeRecord()->appendResponse(204);
        $response = $this->client->update($this->record);
        $this->assertSame(204, $response->getPsrResponse()->getStatusCode());
    }

    public function testDelete()
    {
        $this->appendResponse(204);
        $response = $this->client->delete($this->record);
        $this->assertSame(204, $response->getPsrResponse()->getStatusCode());
        $this->assertRequested('DELETE', '/posts/1');
    }

    /**
     * @param EncodingParametersInterface|null $parameters
     * @return $this
     */
    private function willSerializeRecord(EncodingParametersInterface $parameters = null)
    {
        $this->encoder
            ->expects($this->once())
            ->method('serializeData')
            ->with($this->record, $parameters)
            ->willReturn(['data' => (array) $this->record]);

        return $this;
    }

    /**
     * @param int $status
     * @return $this
     */
    private function willSeeRecord($status = 200)
    {
        $this->appendResponse($status, ['Content-Type' => 'application/vnd.api+json'], [
            'data' => (array) $this->record,
        ]);

        return $this;
    }

    /**
     * @param int $status
     * @param array $headers
     * @param array|null $body
     * @return $this
     */
    private function appendResponse($status = 200, array $headers = [], array $body = null)
    {
        if (is_array($body)) {
            $body = json_encode($body);
        }

        $this->mock->append(new Response($status, $headers, $body));

        return $this;
    }

    /**
     * @return void
     */
    private function assertRequestSentRecord()
    {
        $request = $this->mock->getLastRequest();
        $this->assertJsonStringEqualsJsonString(json_encode([
            'data' => (array) $this->record,
        ]), (string) $request->getBody());
    }

    /**
     * @param $method
     * @param $path
     * @return void
     */
    private function assertRequested($method, $path)
    {
        $uri = 'http://localhost/api/v1' . $path;
        $request = $this->mock->getLastRequest();
        $this->assertEquals($method, $request->getMethod());
        $this->assertEquals($uri, (string) $request->getUri(), 'request uri');
    }

    /**
     * @param $key
     * @param $expected
     * @return void
     */
    private function assertHeader($key, $expected)
    {
        $request = $this->mock->getLastRequest();
        $actual = $request->getHeaderLine($key);
        $this->assertSame($expected, $actual);
    }

    /**
     * @param array $expected
     * @return void
     */
    private function assertQueryParameters(array $expected)
    {
        $query = $this->mock->getLastRequest()->getUri()->getQuery();
        $this->assertEquals($expected, parse_query($query));
    }
}
