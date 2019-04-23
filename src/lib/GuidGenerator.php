<?php
/**
 * NovaeZFormBuilderMigrationBundle.
 *
 * @package   NovaeZFormBuilderMigrationBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZFormBuilderMigrationBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzFormBuilderMigration;

class GuidGenerator
{
    public static function generate(string $prefix = 'guid-'): string
    {
        return implode(
            '',
            [
                $prefix,
               self::makeGuidPiece(),
               self::makeGuidPiece(true),
               self::makeGuidPiece(true),
               self::makeGuidPiece(),
            ]
        );
    }

    /**
     * @return bool|string
     */
    protected static function makeGuidPiece(bool $addDashes = false)
    {
        $random = (float) mt_rand() / (float) mt_getrandmax();
        $piece  = substr(base_convert((string) $random, 10, 16).'000000000', 0, 8);

        return $addDashes ? '-'.substr($piece, 0, 4).'-'.substr($piece, 4, 4) : $piece;
    }
}
