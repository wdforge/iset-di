<?php

namespace Iset\Di\Exception;


class NotFoundMethod extends LoggedException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Method %s is not found in object', $message), $code, $previous);
    }

}