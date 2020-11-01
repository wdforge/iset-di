<?php

namespace Iset\Di\Exception;


class NotFoundFile extends LoggedException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('File %s not found', $message), $code, $previous);
    }
}