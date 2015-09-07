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

namespace CloudCreativity\JsonApi\Responses;

use Neomerx\JsonApi\Contracts\Responses\ResponsesInterface;

/**
 * Class ResponsesAwareTrait
 * @package CloudCreativity\JsonApi
 */
trait ResponsesAwareTrait
{

    /**
     * @var ResponsesInterface
     */
    protected $_responses;

    /**
     * @param ResponsesInterface $responses
     * @return $this
     */
    public function setResponses(ResponsesInterface $responses)
    {
        $this->_responses = $responses;

        return $this;
    }

    /**
     * @return ResponsesInterface
     */
    public function getResponses()
    {
        if (!$this->_responses instanceof ResponsesInterface) {
            throw new \RuntimeException(sprintf('%s expects to be injected with a %s instance.', static::class, ResponsesInterface::class));
        }

        return $this->_responses;
    }
}
