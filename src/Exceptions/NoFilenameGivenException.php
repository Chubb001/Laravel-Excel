<?php

namespace Chubb001\Excel31\Exceptions;

use Throwable;
use InvalidArgumentException;

class NoFilenameGivenException extends InvalidArgumentException implements LaravelExcelException
{
    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        $message = 'A filename needs to be passed in order to download the export',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
