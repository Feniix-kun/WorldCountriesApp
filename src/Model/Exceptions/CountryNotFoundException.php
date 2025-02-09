<?php

namespace App\Model\Exceptions;

use Throwable;
use Exception;

class CountryNotFoundException extends Exception {

    // переопределение конструктора исключения
    public function __construct($code, Throwable $previous = null) {
        $exceptionMessage = "country '". $code ."' not found";
        // вызов конструктора базового класса исключения
        parent::__construct(
            message: $exceptionMessage, 
            code: ErrorCodes::COUNTRY_NOT_FOUND_ERROR,
            previous: $previous,
        );
    }
}
