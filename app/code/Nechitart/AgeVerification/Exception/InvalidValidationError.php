<?php

namespace Nechitart\AgeVerification\Exception;

use Exception;
use Throwable;

class InvalidValidationError extends Exception
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        if (is_array($message)) {
            $message = implode(';', $message);
        }

        parent::__construct($message, $code, $previous);
    }
}
