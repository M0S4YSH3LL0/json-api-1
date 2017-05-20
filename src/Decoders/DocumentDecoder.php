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

namespace CloudCreativity\JsonApi\Decoders;

use CloudCreativity\JsonApi\Contracts\Object\DocumentInterface;
use CloudCreativity\JsonApi\Decoders\Helpers\DecodesJson;
use CloudCreativity\JsonApi\Exceptions\InvalidJsonException;
use CloudCreativity\JsonApi\Object\Document;
use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface;

/**
 * Class ResourceDecoder
 *
 * @package CloudCreativity\JsonApi
 */
class DocumentDecoder implements DecoderInterface
{

    use DecodesJson;

    /**
     * @param string $content
     * @return DocumentInterface
     * @throws InvalidJsonException
     */
    public function decode($content)
    {
        $obj = $this->decodeJson($content);

        return new Document($obj);
    }

}
