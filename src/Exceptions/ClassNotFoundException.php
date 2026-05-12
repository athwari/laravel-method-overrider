<?php

namespace Athwari\MethodOverrider\Exceptions;

use Exception;

class ClassNotFoundException extends Exception
{
    public function __construct(string $class)
    {
        parent::__construct("Class [{$class}] not found.");
    }
}
