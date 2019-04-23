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

namespace Novactive\EzFormBuilderMigration\Converter\EzStudioForm;

use EzSystems\EzPlatformFormBuilder\FieldType\Model\Validator;
use Novactive\EzFormBuilderMigration\Model\Validator as EzStudioFormFieldValidator;

interface ValidatorConverterInterface
{
    public function convert(EzStudioFormFieldValidator $ezStudioFormFieldValidator): ?Validator;

    public function support(string $type): bool;
}
