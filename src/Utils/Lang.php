<?php

namespace Elegance\IdentityProvider\Utils;


use Exception;
use Symfony\Component\Yaml\Yaml;

class Lang {
    protected static array $messages;
    
    public function  __construct(string $filePath) {
        if(!file_exists($filePath))
            throw new Exception("Lang file not found: {$filePath}");

        self::$messages = Yaml::parseFile($filePath);
    }

    public static function get(string $code, array $replace = []): string
    {
        $parts = explode('.', $code);
        $message = self::$messages;

        foreach ($parts as $part) {
            $message = $message[$part] ?? null;
            if ($message === null) {
                return $code;
            }
        }

        foreach ($replace as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }

        return $message;
    }
}