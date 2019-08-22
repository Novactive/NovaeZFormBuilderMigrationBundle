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
use Novactive\EzFormBuilderMigration\GuidGenerator;
use Novactive\EzFormBuilderMigration\Model\Field as EzStudioField;

class SingleLineHiddenField extends DefaultField
{
    public function convert(EzStudioField $ezStudioField): Field
    {
        $options    = $ezStudioField->getOptions();
        $attributes = [
            new Attribute(
                'inputname',
                isset($options['inputname']) ? $options['inputname']->getValue() : null
            ),
            new Attribute(
                'value',
                isset($options['inputvalue']) ? $options['inputvalue']->getValue() : null
            )
        ];

        return new Field(
            GuidGenerator::generate('fbf-'),
            $this->ezPlatformType,
            $ezStudioField->getName(),
            $attributes
        );
    }
}
