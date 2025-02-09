<?php

namespace App\Controller;

class CountryInfo{
    public function __construct(
        public string $shortName,
        public string $fullName,
        public int $population,
        public float $square
    )
    {

    }
}



