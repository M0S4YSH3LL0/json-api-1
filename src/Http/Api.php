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

namespace CloudCreativity\JsonApi\Http;

use CloudCreativity\JsonApi\Contracts\Http\ApiInterface;
use CloudCreativity\JsonApi\Contracts\Repositories\ErrorRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Store\StoreInterface;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\SupportedExtensionsInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface as SchemaContainerInterface;

/**
 * Class Api
 *
 * @package CloudCreativity\JsonApi
 */
class Api implements ApiInterface
{

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var CodecMatcherInterface
     */
    private $codecMatcher;

    /**
     * @var SchemaContainerInterface
     */
    private $schemas;

    /**
     * @param StoreInterface
     */
    private $store;

    /**
     * @var ErrorRepositoryInterface
     */
    private $errors;

    /**
     * @var string|null
     */
    private $urlPrefix;

    /**
     * @var SupportedExtensionsInterface|null
     */
    private $supportedExtensions;

    /**
     * ApiContainer constructor.
     *
     * @param string $namespace
     * @param CodecMatcherInterface $codecMatcher
     * @param SchemaContainerInterface $schemaContainer
     * @param StoreInterface $store
     * @param ErrorRepositoryInterface $errorRepository
     * @param SupportedExtensionsInterface|null $supportedExtensions
     * @param string|null $urlPrefix
     */
    public function __construct(
        $namespace,
        CodecMatcherInterface $codecMatcher,
        SchemaContainerInterface $schemaContainer,
        StoreInterface $store,
        ErrorRepositoryInterface $errorRepository,
        SupportedExtensionsInterface $supportedExtensions = null,
        $urlPrefix = null
    ) {
        $this->namespace = $namespace;
        $this->codecMatcher = $codecMatcher;
        $this->schemas = $schemaContainer;
        $this->store = $store;
        $this->errors = $errorRepository;
        $this->supportedExtensions = $supportedExtensions;
        $this->urlPrefix = $urlPrefix;
    }

    /**
     * @inheritdoc
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @inheritdoc
     */
    public function getCodecMatcher()
    {
        return $this->codecMatcher;
    }

    /**
     * @inheritdoc
     */
    public function getEncoder()
    {
        return $this->getCodecMatcher()->getEncoder();
    }

    /**
     * @inheritdoc
     */
    public function hasEncoder()
    {
        return $this->getEncoder() instanceof EncoderInterface;
    }

    /**
     * @inheritdoc
     */
    public function getSchemas()
    {
        return $this->schemas;
    }

    /**
     * @inheritdoc
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @inheritDoc
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @inheritdoc
     */
    public function getUrlPrefix()
    {
        return $this->urlPrefix;
    }

    /**
     * @inheritdoc
     */
    public function getSupportedExts()
    {
        return $this->supportedExtensions;
    }
}
