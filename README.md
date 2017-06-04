# Better Entities

A talk looking at how to better structure entities using an example from the Symfony documentation:
http://symfony.com/doc/current/best_practices/business-logic.html#doctrine-mapping-information

These files are an example to go along with the talk.

THIS IS FOR EXAMPLE ONLY -- USE AT YOUR OWN DISCRETION!

## Requirements

 * PHP 7+
 * bcmath extension
 * composer
 * Doctrine (for persistence)
 * beberlei/assert (for assertions)
 * somnambulist/collection (for immutable collection)
 * PHPUnit 6

## Installation / Setup

 * `git clone https://github.com/dave-redfern/better-entities.git`
 * `composer install`
 * `vendor/bin/phpunit`

Main code is in the `src` folder, with Doctrine mapping files in `config`.

## Unit Tests

Unit tests are included for most of the code, including integration and persistence tests with Doctrine.

    vendor/bin/phpunit

Run specific group:

    vendor/bin/phpunit --group=entities

