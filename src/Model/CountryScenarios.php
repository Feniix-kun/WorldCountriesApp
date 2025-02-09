<?php

namespace App\Model;

use App\Model\Exceptions\CountryNotFoundException;
use App\Model\Exceptions\DuplicatedDataException;
use App\Model\Exceptions\InvalidCodeException;
use App\Model\Exceptions\InvalidDataException;
use Exception;
use phpDocumentor\Reflection\Types\Boolean;

class CountryScenarios{

    private const FIELD_SHORT_NAME = 'short_name_f';
    private const FIELD_FULL_NAME = 'full_name_f';
    private const FIELD_ISO_ALPHA2 = 'iso_alpha2_f';
    private const FIELD_ISO_ALPHA3= 'iso_alpha3_f';
    private const FIELD_ISO_NUMERIC = 'iso_numeric_f';
    private const FIELD_POPULATION = 'population_f';
    private const FIELD_SQUARE = 'square_f';

    public function __construct(
        private readonly CountryRepository $countryRepository
    )
    {
        
    }


    // getAll - получение всех стран
    // вход: - 
    // выход: - массив объектов стран
    public function getAll(): array{
        return $this->countryRepository->selectAll();
    }

    // get - получение страны по коду
    // вход: - код страны
    // выход: -
    // исключения: InvalidCodeException, CountryNotFoundException
    public function get(string $code) : Country {
        $codeType = $this->validateCode($code);
        if($codeType == ''){
            throw new InvalidCodeException($code, 'validation failed');
        }
        $country = $this->countryRepository->selectByCode($codeType, $code);
        if(!$country){
            throw new CountryNotFoundException($code);
        }
        return $country;
    }

    // add - добавление новой страны
    // вход: объект страны
    // выход: -
    // исключения: InvalidCodeException, InvalidDataException
    public function add(Country $country) : void{
        $this->validateData('short_name_f', $country->shortName);
        $this->validateData('full_name_f', $country->fullName);
        $this->validateData('iso_alpha2_f', $country->isoAlpha2);
        $this->validateData('iso_alpha3_f', $country->isoAlpha3);
        $this->validateData('iso_numeric_f', $country->isoNumeric);
        $this->validateData('population_f', $country->population);
        $this->validateData('square_f', $country->square);
        $this->getByNameAndCode($country);
        // если нигде не вылетел exception то сохранить страну
        $this->countryRepository->save($country);
    }

    // edit - редактирование страны по коду
    // вход: - код страны, объект страны
    // выход: -
    // исключения: InvalidDataException
    public function edit(Country $country) : void {
        $this->validateData('short_name_f', $country->shortName);
        $this->validateData('full_name_f', $country->fullName);
        $this->validateData('population_f', $country->population);
        $this->validateData('square_f', $country->square);
        $this->countryRepository->update($country);
    }

    // delete - удаление страны по коду
    // вход: - код страны
    // выход: -
    // исключения: InvalidCodeException, CountryNotFoundException
    public function remove(string $code) : void {
        $codeType = $this->validateCode($code);
        if($codeType == ''){
            throw new InvalidCodeException($code, 'validation failed');
        }
        $country = $this->countryRepository->selectByCode($codeType, $code);
        if(!$country){
            throw new CountryNotFoundException($code);
        }
        $this->countryRepository->delete($codeType, $code);
    }

    // Метод определяющий по какому типу кода стоит выполнить поиск
    private function validateCode(string $code): string {
        if(preg_match('/^[a-zA-z]{2}$/', $code)){
            return "iso_alpha2_f";
        } else if(preg_match('/^[a-zA-Z]{3}$/', $code)){
            return "iso_alpha3_f";
        } else if(preg_match('/^[0-9]{3}$/', $code)){
            return "iso_numeric_f";
        }
        return '';
    }

    // метод проверки данных
    private function validateData(string $fieldName, string $data) : void {
        if($fieldName == self::FIELD_SHORT_NAME || $fieldName == self::FIELD_FULL_NAME){
            if($data == '' || ctype_space($data)){
                throw new InvalidDataException($fieldName, $data, 'validation failed');
            }
        }
        if($fieldName == self::FIELD_ISO_ALPHA2 || $fieldName == self::FIELD_ISO_ALPHA3 || $fieldName == self::FIELD_ISO_NUMERIC){
            if($this->validateCode($data) == ''){
                throw new InvalidDataException($fieldName, $data, 'validation failed');
            }
        }
        if($fieldName == self::FIELD_POPULATION || $fieldName == self::FIELD_SQUARE){
            if($data < 1){
                throw new InvalidDataException($fieldName, $data, 'validation failed');
            }
        }
    }
    // метод проверки на уникальность
    private function getByNameAndCode(Country $country): void {
        $checkCountry = $this->countryRepository->selectByNameAndCode($country);
        if($checkCountry !== null){
            if($checkCountry !== null){
                if($country->shortName == $checkCountry->shortName){
                    throw new DuplicatedDataException(self::FIELD_SHORT_NAME, $country->shortName, 
                    'country with this value already exists.');
                } else if($country->fullName == $checkCountry->fullName){
                    throw new DuplicatedDataException(self::FIELD_FULL_NAME, $country->fullName, 
                    'country with this value already exists.');
                } else if($country->isoAlpha2 == $checkCountry->isoAlpha2){
                    throw new DuplicatedDataException(self::FIELD_ISO_ALPHA2, $country->isoAlpha2, 
                    'country with this value already exists.');
                } else if($country->isoAlpha3 == $checkCountry->isoAlpha3){
                    throw new DuplicatedDataException(self::FIELD_ISO_ALPHA3, $country->isoAlpha3, 
                    'country with this value already exists.');
                } else if($country->isoNumeric == $checkCountry->isoNumeric){
                    throw new DuplicatedDataException(self::FIELD_ISO_NUMERIC, $country->isoNumeric, 
                    'country with this value already exists.');
                }
            }
        }
    }
}