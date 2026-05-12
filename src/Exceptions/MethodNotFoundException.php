<?php

namespace Athwari\MethodOverrider\Exceptions;

use Exception;

class MethodNotFoundException extends Exception
{
    public function __construct(string $method)
    {
        parent::__construct("Method [{$method}] not found.");
    }
}
