<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2019 osCommerce; https://www.oscommerce.com
 * @license MIT; https://www.oscommerce.com/license/mit.txt
 */

namespace osCommerce\OM\Core;

class FileSystem
{
    public static function getDirectoryContents(string $base, bool $recursive = true): array
    {
        $base = str_replace('\\', '/', $base); // Unix style directory separator "/"

        $flags = \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_SELF | \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS;

        if ($recursive === true) {
            $dir = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($base, $flags));
        } else {
            $dir = new \FilesystemIterator($base, $flags);
        }

        $result = [];

        foreach ($dir as $file) {
            $result[] = $file->getPathName();
        }

        return $result;
    }

    public static function isWritable(string $location, bool $recursive_check = false): bool
    {
        if ($recursive_check === true) {
            if (!file_exists($location)) {
                while (true) {
                    $location = dirname($location);

                    if (file_exists($location)) {
                        break;
                    }
                }
            }
        }

        return is_writable($location);
    }

    public static function rmdir(string $dir, bool $dry_run = false): array
    {
        $result = [];

        if (is_dir($dir)) {
            foreach (static::getDirectoryContents($dir, false) as $file) {
                if (is_dir($file)) {
                    $result = array_merge($result, static::rmdir($file, $dry_run));
                } else {
                    $result[] = [
                        'type' => 'file',
                        'source' => $file,
                        'result' => ($dry_run === false) ? unlink($file) : static::isWritable($file)
                    ];
                }
            }

            $result[] = [
                'type' => 'directory',
                'source' => $dir,
                'result' => ($dry_run === false) ? rmdir($dir) : static::isWritable($dir)
            ];
        }

        return $result;
    }

    public static function isDirectoryEmpty(string $directory): bool
    {
        $dir = new \FilesystemIterator($directory, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_SELF | \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS);

        return ($dir->valid() === false);
    }

    public static function displayPath(string $pathname): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $pathname);
    }

    public static function copyFile(string $source, string $destination): bool
    {
        $target_dir = dirname($destination);

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        return copy($source, $destination);
    }

    public static function moveFile(string $source, string $destination): bool
    {
        $target_dir = dirname($destination);

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (copy($source, $destination)) {
            unlink($source);

            return true;
        }

        return false;
    }
}
