<?php

namespace App\Rdb;

use App\Model\Country;
use App\Model\CountryRepository;
use Exception;


class CountryStorage implements CountryRepository{

    public function __construct(
        private readonly SqlHelper $sqlHelper
    )
    {
    }

    public function selectAll(): array{
        try {
            $connection = $this->sqlHelper->openDbConnection();
            $queryStr = 'SELECT short_name_f, full_name_f, iso_alpha2_f, iso_alpha3_f, iso_numeric_f,
            population_f, square_f
            FROM country_t';
            $rows = $connection->query(query: $queryStr);
            $countries = [];
            while ($row = $rows->fetch_array()){
                $country = new Country(
                    shortName: $row[0],
                    fullName: $row[1],
                    isoAlpha2: $row[2],
                    isoAlpha3: $row[3],
                    isoNumeric:$row[4],
                    population: intval($row[5]),
                    square: floatval($row[6])
                );
                array_push($countries, $country);
            }
            return $countries;
        } finally {
            if (isset($connection)) {
                $connection->close();
            }
        }
    }

    public function selectByCode(string $codeType, string $code): ?Country
    {
       try{
        $connection = $this->sqlHelper->openDbConnection();
        $queryStr = "SELECT short_name_f, full_name_f, iso_alpha2_f, iso_alpha3_f, iso_numeric_f,
            population_f, square_f
            FROM country_t
            WHERE $codeType = ?";
        $query = $connection->prepare(query: $queryStr);
        $query->bind_param('s', $code);
        $query->execute();
        $row = $query->get_result()->fetch_assoc();
        if($row){
            return new Country(
                shortName: $row['short_name_f'],
                fullName: $row['full_name_f'],
                isoAlpha2: $row['iso_alpha2_f'],
                isoAlpha3: $row['iso_alpha3_f'],
                isoNumeric: $row['iso_numeric_f'],
                population: intval($row['population_f']),
                square: floatval($row['square_f'])
            );
        }
        return null;
       } finally {
        if(isset($connection)){
            $connection->close();
        }
       }
    }

    public function save(Country $country): void
    {
        try{
            $connection = $this->sqlHelper->openDbConnection();
            $queryStr = "INSERT INTO country_t(short_name_f, full_name_f, iso_alpha2_f, iso_alpha3_f, iso_numeric_f, population_f, square_f)
                VALUES(?, ?, ?, ?, ?, ?, ?);";
            $query = $connection->prepare(query: $queryStr);
            $query->bind_param('sssssid', $country->shortName, $country->fullName, $country->isoAlpha2, 
                $country->isoAlpha3, $country->isoNumeric, $country->population, $country->square);
            if(!$query->execute()){
                throw new Exception(message: 'insert execute failed');
            }
        } finally {
            if(isset($connection)){
                $connection->close();
        }
    }
}
    
    public function delete(string $codeType, string $code): void
    {
        try {
            $connection = $this->sqlHelper->openDbConnection();
            $queryStr = "DELETE FROM country_t
                WHERE $codeType = ?;";
            $query = $connection->prepare(query: $queryStr);
            $query->bind_param('s', $code);
            if(!$query->execute()){
                throw new Exception(message: 'insert execute failed');
            } 
        } finally {
            if(isset($connection)){
                $connection->close();
            }
        }
    }

    public function update(Country $country): void
    {
        try{
            $connection = $this->sqlHelper->openDbConnection();
            $queryStr = "UPDATE country_t
                SET short_name_f = ?, full_name_f = ?, population_f = ?, square_f = ?
                WHERE iso_alpha2_f = ?;";
            $query = $connection->prepare(query: $queryStr);
            $query->bind_param('ssids', $country->shortName, $country->fullName, 
            $country->population, $country->square, $country->isoAlpha2,);
            if(!$query->execute()){
                throw new Exception(message: 'insert execute failed');
            }
        } finally {
            if(isset($connection)){
                $connection->close();
            }
        }
    }

    public function selectByNameAndCode(Country $country): ?Country {
        try{
            $connection = $this->sqlHelper->openDbConnection();
            $queryStr = "SELECT short_name_f, full_name_f, iso_alpha2_f, iso_alpha3_f, iso_numeric_f,
                population_f, square_f
                FROM country_t
                WHERE short_name_f = ? OR full_name_f = ? OR iso_alpha2_f = ? OR iso_alpha3_f = ? OR iso_numeric_f = ?";
            $query = $connection->prepare(query: $queryStr);
            $query->bind_param('sssss', $country->shortName, $country->fullName, 
                $country->isoAlpha2, $country->isoAlpha3, $country->isoNumeric);
            $query->execute();
            $row = $query->get_result()->fetch_assoc();
            if($row){
                return new Country(
                    shortName: $row['short_name_f'],
                    fullName: $row['full_name_f'],
                    isoAlpha2: $row['iso_alpha2_f'],
                    isoAlpha3: $row['iso_alpha3_f'],
                    isoNumeric: $row['iso_numeric_f'],
                    population: intval($row['population_f']),
                    square: floatval($row['square_f'])
                );
            }
            return null;
           } finally {
               if(isset($connection)){
                $connection->close();
            }
        }
    }
}