<?php

namespace App\Persistence\Exception;

final class NoVariantException extends \RuntimeException
{
    public function __construct(int $id, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(\sprintf('No variant for ID=%s', $id), $code, $previous);
    }
}