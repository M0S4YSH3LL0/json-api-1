<?php

/**
 * Copyright 2016 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Contracts\Http;

use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\SupportedExtensionsInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface as SchemaContainerInterface;

/**
 * Interface ApiInterface
 * @package CloudCreativity\JsonApi
 */
interface ApiInterface
{

    /**
     * Get the unique name for the API instance.
     *
     * @return string
     */
    public function name();

    /**
     * Get the codec matcher for this API.
     *
     * @return CodecMatcherInterface
     */
    public function codecMatcher();

    /**
     * Get the encoder for this API.
     *
     * @return EncoderInterface|null
     */
    public function encoder();

    /**
     * Get the schema container for this API.
     *
     * @return SchemaContainerInterface
     */
    public function schemas();

    /**
     * Get the URL prefix for this API.
     *
     * @return string|null
     */
    public function urlPrefix();

    /**
     * Get the supported extensions for this API.
     *
     * @return SupportedExtensionsInterface|null
     */
    public function supportedExts();
}
