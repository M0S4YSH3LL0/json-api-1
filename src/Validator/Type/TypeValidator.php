<?php

namespace CloudCreativity\JsonApi\Validator\Type;

use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigurableInterface;
use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Validator\AbstractValidator;

class TypeValidator extends AbstractValidator implements ConfigurableInterface
{

    use NullableTrait;

    // Config Constants
    const ACCEPT_NULL = 'acceptNull';

    // Error Constants
    const ERROR_INVALID_VALUE = 'invalid-value';

    /**
     * @var array
     */
    protected $templates = [
        self::ERROR_INVALID_VALUE => [
            ErrorObject::CODE => self::ERROR_INVALID_VALUE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid Value',
        ],
    ];

    /**
     * @param bool $nullable
     */
    public function __construct($nullable = false)
    {
        $this->setAcceptNull($nullable);
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
        if (array_key_exists(static::ACCEPT_NULL, $config)) {
            $this->setAcceptNull($config[static::ACCEPT_NULL]);
        }

        return $this;
    }

    /**
     * @param $value
     */
    protected function validate($value)
    {
        if (is_null($value) && $this->isNullAllowed()) {
            return;
        }

        if (!$this->isType($value)) {
            $this->error(static::ERROR_INVALID_VALUE);
        }
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isType($value)
    {
        return true;
    }
}