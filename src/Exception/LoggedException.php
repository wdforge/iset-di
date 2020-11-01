<?php

namespace Iset\Di\Exception;

use Iset\Utils\Logger;
use Throwable;

class LoggedException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        Logger::Log(sprintf("Error: %s, Code: %d", $message, $code));
        parent::__construct($message, $code, $previous);
    }
}