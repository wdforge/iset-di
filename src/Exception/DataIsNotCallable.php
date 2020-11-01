<?php

namespace Iset\Di\Exception;


use Throwable;

class DataIsNotCallable extends LoggedException
{
    public function __construct($message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Data %s is not callable', strval($message)), $code, $previous);
    }
}