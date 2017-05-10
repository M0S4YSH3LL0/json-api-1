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

namespace CloudCreativity\JsonApi\Factories;

use CloudCreativity\JsonApi\Contracts\Factories\FactoryInterface;
use CloudCreativity\JsonApi\Contracts\Http\ApiInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterpreterInterface;
use CloudCreativity\JsonApi\Contracts\Pagination\PageInterface;
use CloudCreativity\JsonApi\Contracts\Repositories\ErrorRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Store\ContainerInterface as AdapterContainerInterface;
use CloudCreativity\JsonApi\Contracts\Store\StoreInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorErrorFactoryInterface;
use CloudCreativity\JsonApi\Encoder\Encoder;
use CloudCreativity\JsonApi\Http\Api;
use CloudCreativity\JsonApi\Http\Requests\RequestFactory;
use CloudCreativity\JsonApi\Pagination\Page;
use CloudCreativity\JsonApi\Repositories\CodecMatcherRepository;
use CloudCreativity\JsonApi\Repositories\ErrorRepository;
use CloudCreativity\JsonApi\Store\Container as AdapterContainer;
use CloudCreativity\JsonApi\Store\Store;
use CloudCreativity\JsonApi\Utils\Replacer;
use CloudCreativity\JsonApi\Validators\ValidatorErrorFactory;
use CloudCreativity\JsonApi\Validators\ValidatorFactory;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Document\LinkInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\SupportedExtensionsInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface as SchemaContainerInterface;
use Neomerx\JsonApi\Encoder\EncoderOptions;
use Neomerx\JsonApi\Factories\Factory as BaseFactory;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Factory
 *
 * @package CloudCreativity\JsonApi
 */
class Factory extends BaseFactory implements FactoryInterface
{

    /**
     * @param SchemaContainerInterface $container
     * @param EncoderOptions|null $encoderOptions
     * @return Encoder
     */
    public function createEncoder(SchemaContainerInterface $container, EncoderOptions $encoderOptions = null)
    {
        $encoder = new Encoder($this, $container, $encoderOptions);
        $encoder->setLogger($this->logger);

        return $encoder;
    }

    /**
     * @inheritDoc
     */
    public function createApi(
        $namespace,
        CodecMatcherInterface $codecMatcher,
        SchemaContainerInterface $schemaContainer,
        StoreInterface $store,
        ErrorRepositoryInterface $errorRepository,
        SupportedExtensionsInterface $supportedExtensions = null,
        $urlPrefix = null
    ) {
        return new Api(
            $namespace,
            $codecMatcher,
            $schemaContainer,
            $store,
            $errorRepository,
            $supportedExtensions,
            $urlPrefix
        );
    }

    /**
     * @inheritDoc
     */
    public function createRequest(
        ServerRequestInterface $httpRequest,
        RequestInterpreterInterface $intepreter,
        ApiInterface $api
    ) {
        $requestFactory = new RequestFactory($this);

        return $requestFactory->build($httpRequest, $intepreter, $api);
    }

    /**
     * @inheritDoc
     */
    public function createConfiguredCodecMatcher(SchemaContainerInterface $schemas, array $codecs, $urlPrefix = null)
    {
        $repository = new CodecMatcherRepository($this);
        $repository->configure($codecs);

        return $repository
            ->registerSchemas($schemas)
            ->registerUrlPrefix($urlPrefix)
            ->getCodecMatcher();
    }

    /**
     * @inheritdoc
     */
    public function createStore(AdapterContainerInterface $adapters)
    {
        return new Store($adapters);
    }

    /**
     * @inheritDoc
     */
    public function createAdapterContainer(array $adapters)
    {
        $container = new AdapterContainer();
        $container->registerMany($adapters);

        return $container;
    }

    /**
     * @inheritDoc
     */
    public function createErrorRepository(array $errors)
    {
        $repository = new ErrorRepository($this->createReplacer());
        $repository->configure($errors);

        return $repository;
    }

    /**
     * @inheritDoc
     */
    public function createReplacer()
    {
        return new Replacer();
    }

    /**
     * @inheritDoc
     */
    public function createValidatorFactory(ErrorRepositoryInterface $errors, StoreInterface $store)
    {
        $errors = new ValidatorErrorFactory($errors);

        return new ValidatorFactory($errors, $store);
    }

    /**
     * @inheritDoc
     */
    public function createPage(
        $data,
        LinkInterface $first = null,
        LinkInterface $previous = null,
        LinkInterface $next = null,
        LinkInterface $last = null,
        $meta = null,
        $metaKey = null
    ) {
        return new Page($data, $first, $previous, $next, $last, $meta, $metaKey);
    }

}
