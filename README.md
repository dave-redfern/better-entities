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

## Slides

The slides are available in `/docs`. These were exported from Keynote, unfortunately there appears to be no way
to preserve all the contents of the presenter notes so some of the text was truncated. This only affects the
source PHP code. That has been included as a separate file (`source_post.php`).

There are 2 versions of the slides:

 * Just the slides
 * Slides + presenter notes with links and extra information

You may re-produce this talk provided the copyright notices remain intact and it is fully credited and linked
to the GitHub repository.
