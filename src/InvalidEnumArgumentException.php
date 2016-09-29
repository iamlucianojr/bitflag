<?php

namespace Lucianojr\BitFlag;

class InvalidEnumArgumentException extends \InvalidArgumentException
{

    /**
     * InvalidEnumArgumentException constructor.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        parent::__construct(sprintf('"%s" is not an acceptable value.', $value));
    }
}
