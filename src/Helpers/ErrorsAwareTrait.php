<?php

namespace CloudCreativity\JsonApi\Helpers;

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Document\Error;
use Neomerx\JsonApi\Exceptions\ErrorCollection;

trait ErrorsAwareTrait
{

    /**
     * @var ErrorCollection|null
     */
    private $errors;

    /**
     * @return ErrorCollection
     */
    public function errors()
    {
        if (!$this->errors instanceof ErrorCollection) {
            $this->errors = new ErrorCollection();
        }

        return $this->errors;
    }

    /**
     * @param ErrorInterface $error
     * @return $this
     */
    public function addError(ErrorInterface $error)
    {
        // @todo https://github.com/neomerx/json-api/issues/141
        if ($error instanceof Error) {
            $this->errors()->add($error);
        }

        return $this;
    }

    /**
     * @param ErrorCollection $errors
     * @return $this
     */
    public function addErrors(ErrorCollection $errors)
    {
        /** @var Error $error */
        foreach ($errors as $error) {
            $this->errors()->add($error);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function reset()
    {
        $this->errors = null;

        return $this;
    }
}
