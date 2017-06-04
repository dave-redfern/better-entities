<?php

namespace AppBundle\Support;

/**
 * Class Slugger
 *
 * @package    AppBundle\Support
 * @subpackage AppBundle\Support\Slugger
 */
final class Slugger
{

    /**
     * Derived from Laravel Str::slug()
     *
     * @param string $title
     * @param string $separator
     *
     * @return string
     */
    public static function generateSlugFrom(string $title, string $separator = '-'): string
    {
        $separator = '-';
        $flip = $separator == '-' ? '_' : '-';

        $title = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', mb_strtolower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);

        return trim($title, $separator);
    }
}