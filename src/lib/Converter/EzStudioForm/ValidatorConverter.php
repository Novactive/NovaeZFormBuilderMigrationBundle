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

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use EzSystems\EzPlatformFormBuilder\FieldType\Model\Validator;
use Novactive\EzFormBuilderMigration\Model\Validator as EzStudioValidator;

class ValidatorConverter
{
    /** @var iterable */
    protected $converters;

    /**
     * ValidatorConverter constructor.
     */
    public function __construct(iterable $converters)
    {
        $this->converters = $converters;
    }

    /**
     * @throws NotFoundException
     *
     * @return Validator
     */
    public function convert(EzStudioValidator $ezStudioValidator): ?Validator
    {
        $converter = $this->getConverter($ezStudioValidator->getType());

        return $converter->convert($ezStudioValidator);
    }

    /**
     * @throws NotFoundException
     */
    protected function getConverter(string $type): ValidatorConverterInterface
    {
        /** @var ValidatorConverterInterface $converter */
        foreach ($this->converters as $converter) {
            if ($converter->support($type)) {
                return $converter;
            }
        }
        throw new NotFoundException('ValidatorConverterInterface', $type);
    }
}
