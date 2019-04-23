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
use Novactive\EzFormBuilderMigration\Converter\EzStudioForm\FieldConverterInterface;
use Novactive\EzFormBuilderMigration\GuidGenerator;
use Novactive\EzFormBuilderMigration\Model\Field as EzStudioField;

class DefaultField implements FieldConverterInterface
{
    /**
     * Field type in eZStudio form builder.
     *
     * @var string
     */
    protected $ezStudioType;

    /**
     * Field type in eZPlatform form builder.
     *
     * @var string
     */
    protected $ezPlatformType;

    /**
     * DefaultField constructor.
     */
    public function __construct(string $ezStudioType, string $ezPlatformType)
    {
        $this->ezStudioType   = $ezStudioType;
        $this->ezPlatformType = $ezPlatformType;
    }

    public function support(string $type): bool
    {
        return $type === $this->ezStudioType;
    }

    public function convert(EzStudioField $ezStudioField): Field
    {
        $attributes = [
            new Attribute(
                'placeholder',
                $ezStudioField->getPlaceholderText()
            ),
            new Attribute(
                'help',
                $ezStudioField->getHelpText()
            ),
        ];

        return new Field(
            GuidGenerator::generate('fbf-'),
            $this->ezPlatformType,
            $ezStudioField->getName(),
            $attributes
        );
    }
}
