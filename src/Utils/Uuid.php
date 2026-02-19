<?php

namespace Elegance\IdentityProvider\Utils;

/**
 * Class Uuid
 *
 * Simple utility class to generate UUIDs (version 4, random-based).
 */
class Uuid
{
    /**
     * Generate a version 4 UUID
     *
     * Uses random integers to produce a unique UUID in the format:
     * xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
     *
     * @return string Generated UUID
     * @throws \Exception If random_int() fails
     */
    public static function generate(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff), random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0x0fff) | 0x4000, // version 4
            random_int(0, 0x3fff) | 0x8000, // variant 10xx
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
        );
    }
}
