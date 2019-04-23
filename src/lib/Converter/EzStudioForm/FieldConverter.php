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
use EzSystems\EzPlatformFormBuilder\FieldType\Model\Field;
use Novactive\EzFormBuilderMigration\Model\Field as EzStudioField;

class FieldConverter
{
    /** @var ValidatorConverter */
    protected $validatorConverter;

    /** @var iterable */
    protected $converters;

    /**
     * FieldConverter constructor.
     */
    public function __construct(iterable $converters)
    {
        $this->converters = $converters;
    }

    /**
     * @required
     */
    public function setValidatorConverter(ValidatorConverter $validatorConverter): void
    {
        $this->validatorConverter = $validatorConverter;
    }

    /**
     * @throws NotFoundException
     */
    public function convert(EzStudioField $ezStudioField): Field
    {
        $converter = $this->getConverter($ezStudioField->getType());

        $field      = $converter->convert($ezStudioField);
        $validators = [];
        foreach ($ezStudioField->getValidators() as $validator) {
            $validator = $this->validatorConverter->convert($validator);
            if ($validator) {
                $validators[] = $validator;
            }
        }
        $field->setValidators($validators);

        return $field;
    }

    /**
     * @throws NotFoundException
     */
    protected function getConverter(string $type): FieldConverterInterface
    {
        /** @var FieldConverterInterface $converter */
        foreach ($this->converters as $converter) {
            if ($converter->support($type)) {
                return $converter;
            }
        }
        throw new NotFoundException('FieldConverterInterface', $type);
    }
}
