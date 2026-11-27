<?php

namespace App\Exceptions;

use RuntimeException;

/** Thrown when a customer tries to add an item from a different vendor than the one already in the cart. */
class CartVendorConflictException extends RuntimeException
{
    //
}