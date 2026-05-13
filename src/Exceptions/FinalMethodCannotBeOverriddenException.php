<?php

namespace Athwari\MethodOverrider\Exceptions;

use Exception;

class FinalMethodCannotBeOverriddenException extends Exception
{
    public function __construct(string $method)
    {
        parent::__construct(
            "Final method [{$method}] cannot be overridden."
        );
    }
}