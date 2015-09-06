<?php

/**
 * Copyright 2015 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Codec;

use CloudCreativity\JsonApi\Contracts\Validator\ValidatorAwareInterface;
use CloudCreativity\JsonApi\Validator\ValidatorAwareTrait;
use Neomerx\JsonApi\Codec\CodecMatcher as BaseCodecMatcher;
use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface;

/**
 * Class CodecMatcher
 * @package CloudCreativity\JsonApi
 */
class CodecMatcher extends BaseCodecMatcher
{

    use ValidatorAwareTrait;

    /**
     * @return DecoderInterface|null
     */
    public function getDecoder()
    {
        $decoder = parent::getDecoder();

        if ($decoder instanceof ValidatorAwareInterface && $this->hasValidator()) {
            $decoder->setValidator($this->getValidator());
        }

        return $decoder;
    }
}
