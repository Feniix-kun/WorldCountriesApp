<?php

namespace App\Model;

interface CountryRepository {

// selectAll - получение всех стран
function selectAll(): array;

// selectByCode - получение страны по коду
function selectByCode(string $codeType, string $code): ?Country;

// save - сохранение страны в БД
function save(Country $country): void;

// delete - удаление страны по коду
function delete(string $codeType, string $code): void;

// update - обновление данных страны по коду
function update(Country $country): void;

// selectByNameAndCode - проверка на уникальность имен и кодов
function selectByNameAndCode(Country $country): ?Country;
}

