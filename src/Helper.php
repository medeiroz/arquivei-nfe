<?php


namespace MedeirosDev\Arquivei;

use MedeirosDev\Arquivei\Exceptions\NotImplementedException;
use Exception;

class Helper
{
    private function __construct() {}

    public static function existsConstInClassByValue(string $class, string $constValue): bool
    {
        return in_array($constValue, self::getClassConstants($class));
    }

    public static function getClassConstants(string $class): array
    {
        try {
            return (new \ReflectionClass($class))->getConstants();
        } catch (\Exception $e) {}

        return [];
    }

    public static function camelCase(string $str, array $noStrip = []): string
    {
        $str = Normalizer::normalize($str, Normalizer::FORM_D);

        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);

        return $str;
    }

    public static function booleanToString($value)
    {
        return ($value) ? 'true' : 'false';
    }

    public static function getFramework(): string
    {
        if (class_exists('\Illuminate\Support\Facades\Artisan')) {

            \Illuminate\Support\Facades\Artisan::call('help', ['--version']);
            $output = \Illuminate\Support\Facades\Artisan::output();

            if (strpos($output, 'Laravel') >= 0) {
                return 'Laravel';
            }

        }

        return false;
    }

    public static function config(string $path): ?string
    {
        $slugPath = strtoupper(str_replace('.', '_', $path));


        if (Helper::getFramework() === 'Laravel') {
            return config($path);

        } elseif (function_exists('env')) {
            return env($slugPath);

        } elseif (function_exists('getenv')) {
            return getenv($slugPath);

        } else {
            return null;
        }
    }
}
