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

class NullValidator extends DefaultValidator
{
    public function convert(EzStudioValidator $ezStudioValidator): ?Validator
    {
        return null;
    }
}
