<?php

namespace Iset\Di\Exception;


use Throwable;

class FileIsNotWriteable extends LoggedException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('File %s is not writable', $message), $code, $previous);
    }
}