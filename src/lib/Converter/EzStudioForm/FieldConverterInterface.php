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

use EzSystems\EzPlatformFormBuilder\FieldType\Model\Field;
use Novactive\EzFormBuilderMigration\Model\Field as EzStudioField;

interface FieldConverterInterface
{
    public function convert(EzStudioField $ezStudioField): Field;

    public function support(string $type): bool;
}
