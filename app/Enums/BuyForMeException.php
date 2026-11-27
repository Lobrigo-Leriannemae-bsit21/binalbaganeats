<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use RuntimeException;
use Throwable;

/** A user-facing problem with a Buy-for-Me request. The message is safe to show. */
class BuyForMeException extends RuntimeException
{
    /** Turns a duplicate GCash reference database error into a friendly exception (or null). */
    public static function fromDuplicateReference(Throwable $e): ?self
    {
        if ($e instanceof QueryException
            && (int) ($e->errorInfo[1] ?? 0) === 1062
            && str_contains($e->getMessage(), 'gcash_reference_number')) {
            return new self('That GCash reference number has already been used.');
        }

        return null;
    }
}