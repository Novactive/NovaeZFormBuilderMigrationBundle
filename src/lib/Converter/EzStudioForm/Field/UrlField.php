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

namespace Novactive\EzFormBuilderMigration\Converter\EzStudioForm\Field;

use EzSystems\EzPlatformFormBuilder\FieldType\Model\Attribute;
use EzSystems\EzPlatformFormBuilder\FieldType\Model\Field;
use Novactive\EzFormBuilderMigration\Model\Field as EzStudioField;

class UrlField extends DefaultField
{
    public function convert(EzStudioField $ezStudioField): Field
    {
        $field = parent::convert($ezStudioField);

        $attributes   = $field->getAttributes();
        $attributes[] = new Attribute(
            'default_value',
            null
        );
        $field->setAttributes($attributes);

        return $field;
    }
}
