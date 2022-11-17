<?php

namespace VerifyMyContent\Commons\Transport;

use Exception;

class InvalidStatusCodeException extends Exception
{
    public function __construct($code = 0, $message = "", Exception $previous = null)
    {
        if (empty($message)) {
            $message = "Invalid status code: " . $code;
        }
        parent::__construct($message, $code, $previous);
    }
}
