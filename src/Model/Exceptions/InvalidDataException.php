<?php

namespace App\Model\Exceptions;

use Throwable;
use Exception;

class InvalidDataException extends Exception {

    // переопределение конструктора исключения
    public function __construct($dataType ,$data, $message, Throwable $previous = null) {
        $exceptionMessage = "field: ". $dataType . " = '". $data ."' is invalid: ".$message;
        // вызов конструктора базового класса исключения
        parent::__construct(
            message: $exceptionMessage, 
            code: ErrorCodes::INVALID_DATA_ERROR,
            previous: $previous,
        );
    }
}
