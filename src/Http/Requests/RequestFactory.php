<?php

/**
 * Copyright 2017 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CloudCreativity\JsonApi\Http\Requests;

use CloudCreativity\JsonApi\Contracts\Http\ApiInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterpreterInterface;
use CloudCreativity\JsonApi\Contracts\Object\DocumentInterface;
use CloudCreativity\JsonApi\Contracts\Store\StoreInterface;
use CloudCreativity\JsonApi\Exceptions\RuntimeException;
use CloudCreativity\JsonApi\Object\Document;
use CloudCreativity\JsonApi\Object\ResourceIdentifier;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Http\HttpFactoryInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RequestFactory
 *
 * @package CloudCreativity\JsonApi
 */
class RequestFactory
{

    /**
     * @var HttpFactoryInterface
     */
    private $httpFactory;

    /**
     * RequestFactory constructor.
     *
     * @param HttpFactoryInterface $httpFactory
     */
    public function __construct(HttpFactoryInterface $httpFactory)
    {
        $this->httpFactory = $httpFactory;
    }

    /**
     * @param ServerRequestInterface $request
     *      the inbound HTTP request
     * @param RequestInterpreterInterface $interpreter
     *      the intepreter to analyze the request.
     * @param ApiInterface $api
     *      the API that is receiving the request.
     * @return Request
     * @throws JsonApiException
     *      if the request fails content negotiation for the API.
     */
    public function build(
        ServerRequestInterface $request,
        RequestInterpreterInterface $interpreter,
        ApiInterface $api
    ) {
        $this->doContentNegotiation($request, $codecMatcher = $api->getCodecMatcher());

        return new Request(
            $interpreter->getResourceType(),
            $this->parseParameters($request),
            $interpreter->getResourceId(),
            $interpreter->getRelationshipName(),
            $this->parseDocument($request, $interpreter, $codecMatcher),
            $this->locateRecord($interpreter, $api->getStore())
        );
    }

    /**
     * @param ServerRequestInterface $request
     * @param CodecMatcherInterface $codecMatcher
     * @throws JsonApiException
     */
    protected function doContentNegotiation(ServerRequestInterface $request, CodecMatcherInterface $codecMatcher)
    {
        $parser = $this->httpFactory->createHeaderParametersParser();
        $checker = $this->httpFactory->createHeadersChecker($codecMatcher);

        $checker->checkHeaders($parser->parse($request));
    }

    /**
     * @param ServerRequestInterface $request
     * @return EncodingParametersInterface
     * @throws JsonApiException
     */
    protected function parseParameters(ServerRequestInterface $request)
    {
        return $this->httpFactory->createQueryParametersParser()->parse($request);
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestInterpreterInterface $interpreter
     * @param CodecMatcherInterface $codecMatcher
     * @return DocumentInterface
     */
    protected function parseDocument(
        ServerRequestInterface $request,
        RequestInterpreterInterface $interpreter,
        CodecMatcherInterface $codecMatcher
    ) {
        if (!$interpreter->isExpectingDocument()) {
            return null;
        }

        if (!$decoder = $codecMatcher->getDecoder()) {
            throw new RuntimeException('No matching decoder');
        }

        $document = $decoder->decode((string) $request->getBody());

        if (!is_object($document)) {
            throw new RuntimeException('A decoder that decodes to an object must be used.');
        }

        $document = ($document instanceof DocumentInterface) ? $document : new Document($document);

        return $document;
    }

    /**
     * @param RequestInterpreterInterface $interpreter
     * @param StoreInterface $store
     * @return object
     */
    protected function locateRecord(RequestInterpreterInterface $interpreter, StoreInterface $store)
    {
        if (!$id = $interpreter->getResourceId()) {
            return null;
        }

        $identifier = ResourceIdentifier::create($interpreter->getResourceType(), $id);
        $record = $store->find($identifier);

        if (!$record) {
            throw new JsonApiException([], 404);
        }

        return $record;
    }

}
