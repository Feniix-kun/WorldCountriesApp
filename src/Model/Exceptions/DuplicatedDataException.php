<?php

namespace App\Model\Exceptions;

use Throwable;
use Exception;

class DuplicatedDataException extends Exception {

    // переопределение конструктора исключения
    public function __construct($dataType ,$data, $message, Throwable $previous = null) {
        $exceptionMessage = "Country with field: ". $dataType . " = '". $data ."' is invalid: ".$message;
        // вызов конструктора базового класса исключения
        parent::__construct(
            message: $exceptionMessage, 
            code: ErrorCodes::INVALID_DATA_ERROR,
            previous: $previous,
        );
    }
}
