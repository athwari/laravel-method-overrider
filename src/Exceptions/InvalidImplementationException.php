<?php

namespace Athwari\MethodOverrider\Exceptions;

use Exception;

class InvalidImplementationException extends Exception
{
    protected $message = 'Methods count must match implementations count.';
}
