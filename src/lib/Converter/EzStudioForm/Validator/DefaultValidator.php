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
use Novactive\EzFormBuilderMigration\Converter\EzStudioForm\ValidatorConverterInterface;
use Novactive\EzFormBuilderMigration\Model\Validator as EzStudioValidator;

class DefaultValidator implements ValidatorConverterInterface
{
    /**
     * Validor type in eZStudio form builder.
     *
     * @var string
     */
    protected $ezStudioType;

    /**
     * Validor type in eZPlatform form builder.
     *
     * @var string
     */
    protected $ezPlatformType;

    /**
     * DefaultValidator constructor.
     */
    public function __construct(string $ezStudioType, string $ezPlatformType = '')
    {
        $this->ezStudioType   = $ezStudioType;
        $this->ezPlatformType = $ezPlatformType;
    }

    public function support(string $type): bool
    {
        return $type === $this->ezStudioType;
    }

    public function convert(EzStudioValidator $ezStudioValidator): ?Validator
    {
        return new Validator(
            $this->ezPlatformType,
            $ezStudioValidator->getValue()
        );
    }
}
