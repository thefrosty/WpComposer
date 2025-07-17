<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer\Composer;

use function copy;
use function dir;
use function is_dir;
use function is_file;
use function is_link;
use function mkdir;
use function readlink;
use function symlink;

/**
 * Class Scripts
 * @package TheFrosty\WpComposer\Composer
 * @psalm-api
 */
class Scripts
{
    /**
     * Our post composer update command.
     */
    public static function postUpdate(): void
    {
        self::copy('vendor/dwnload/wp-composer/mu-plugins/', 'wp-content/mu-plugins/');
    }

    /**
     * Copy a file, or recursively copy a folder and its contents
     * @param string $source Source path
     * @param string $dest Destination path
     * @return bool Returns TRUE on success, FALSE on failure
     * @author Aidan Lister <aidan@php.net>
     * @version 1.0.1
     * @link http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
     */
    protected static function copy(string $source, string $dest): bool
    {
        // Check for symlinks.
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file.
        if (is_file($source)) {
            return copy($source, $dest);
        }

        // Make destination directory.
        if (!is_dir($dest)) {
            mkdir($dest);
        }

        // Loop through the folder.
        $directory = dir($source);
        while ($directory && ($entry = $directory->read()) !== false) {
            // Skip pointers.
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            // Deep copy directories.
            self::copy("$source/$entry", "$dest/$entry");
        }

        // Clean up.
        $directory->close();
        return true;
    }
}
