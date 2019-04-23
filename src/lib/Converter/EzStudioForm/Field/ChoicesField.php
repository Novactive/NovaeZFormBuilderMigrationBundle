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

class ChoicesField extends DefaultField
{
    public function convert(EzStudioField $ezStudioField): Field
    {
        $field = parent::convert($ezStudioField);

        $attributes = $field->getAttributes();

        $options = $ezStudioField->getOptions();
        $choices = json_decode($options['choices']->getValue() ?? '[]', true);
        $choices = array_map(
            function ($choice) {
                return trim($choice['value']);
            },
            $choices
        );

        $value        = json_encode($choices);
        $attributes[] = new Attribute(
            'options',
            (false !== $value) ? $value : null
        );

        $field->setAttributes($attributes);

        return $field;
    }
}
