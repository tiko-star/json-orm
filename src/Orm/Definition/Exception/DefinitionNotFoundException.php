<?php

declare(strict_types = 1);

namespace App\Orm\Definition\Exception;

use RuntimeException;

/**
 * Any time an invalid definition is requested system should throw this exception.
 *
 * @package App\Orm\Definition\Exception
 */
class DefinitionNotFoundException extends RuntimeException
{
}
