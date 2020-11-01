<?php

namespace Iset\Di\Exception;


use Throwable;

class NotFoundClass extends LoggedException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Class %s is not found', $message), $code, $previous);
    }
}