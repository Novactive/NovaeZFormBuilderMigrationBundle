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

class RegexValidator extends DefaultValidator
{
    public function convert(EzStudioValidator $ezStudioValidator): ?Validator
    {
        $validator = parent::convert($ezStudioValidator);
        if ($validator && false !== $value = json_encode(
            [
                    'select'  => "\/.*\/",
                    'pattern' => $ezStudioValidator->getValue(),
                ]
        )) {
            $validator->setValue($value);
        }

        return $validator;
    }
}
