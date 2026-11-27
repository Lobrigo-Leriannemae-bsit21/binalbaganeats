<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use RuntimeException;
use Throwable;

/** A user-facing problem while placing or changing an order. The message is safe to show. */
class OrderException extends RuntimeException
{
    /** Turns a duplicate GCash reference database error into a friendly exception (or null). */
    public static function fromDuplicateReference(Throwable $e): ?self
    {
        if ($e instanceof QueryException
            && (int) ($e->errorInfo[1] ?? 0) === 1062
            && str_contains($e->getMessage(), 'transaction_reference')) {
            return new self('That GCash reference number has already been used on another order.');
        }

        return null;
    }
}