-- создание БД
DROP DATABASE IF EXISTS world_countries_db;
CREATE DATABASE world_countries_db;
-- переключение на данную БД
USE world_countries_db;
-- создание таблицы стран
CREATE TABLE country_t (
    id INT NOT NULL AUTO_INCREMENT,
    short_name_f NVARCHAR(200) NOT NULL UNIQUE,
    full_name_f NVARCHAR(200) NOT NULL UNIQUE,
    iso_alpha2_f NVARCHAR(2) NOT NULL UNIQUE,
    iso_alpha3_f NVARCHAR(3) NOT NULL UNIQUE,
    iso_numeric_f NVARCHAR(3) NOT NULL UNIQUE,
    population_f BIGINT NOT NULL,
    square_f DECIMAL(15, 2) NOT NULL,
    PRIMARY KEY(id),
    CHECK(population_f > 0 AND square_f > 0)
);
