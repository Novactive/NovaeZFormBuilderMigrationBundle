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

namespace Novactive\EzFormBuilderMigration\Converter\EzStudioForm\Validator;

use EzSystems\EzPlatformFormBuilder\FieldType\Model\Validator;
use Novactive\EzFormBuilderMigration\Model\Validator as EzStudioValidator;

class FileSizeValidator extends DefaultValidator
{
    public function convert(EzStudioValidator $ezStudioValidator): ?Validator
    {
        $sizeString = $ezStudioValidator->getValue();

        $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $size  = null;
        foreach ($units as $unit) {
            $regex = sprintf("/([\d]+)\s*?%s/mi", $unit);

            if (preg_match($regex, $sizeString, $matches)) {
                $size = $matches[1];
            }
        }

        if (!$size) {
            return null;
        }

        return new Validator(
            $this->ezPlatformType,
            $size
        );
    }
}
