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

namespace Novactive\EzFormBuilderMigration\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\AccessType("public_method")
 */
class Field
{
    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setId")
     */
    private $id;

    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setType")
     */
    private $type;

    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setName")
     */
    private $name;

    /**
     * @var string|null
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setHelpText")
     */
    private $helpText;

    /**
     * @var string|null
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setPlaceholderText")
     */
    private $placeholderText;

    /**
     * @var string|null
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setAdminLabel")
     */
    private $adminLabel;

    /**
     * @var Option[]
     * @Serializer\Type("array<string, Novactive\EzFormBuilderMigration\Model\Option>")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setOptions")
     */
    private $options = [];

    /**
     * @var Validator[]
     * @Serializer\Type("array<string, Novactive\EzFormBuilderMigration\Model\Validator>")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setValidators")
     */
    private $validators = [];

    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setView")
     */
    private $view;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getHelpText(): ?string
    {
        return $this->helpText;
    }

    public function setHelpText(?string $helpText): void
    {
        $this->helpText = $helpText;
    }

    public function getPlaceholderText(): ?string
    {
        return $this->placeholderText;
    }

    public function setPlaceholderText(?string $placeholderText): void
    {
        $this->placeholderText = $placeholderText;
    }

    public function getAdminLabel(): ?string
    {
        return $this->adminLabel;
    }

    public function setAdminLabel(?string $adminLabel): void
    {
        $this->adminLabel = $adminLabel;
    }

    /**
     * @return Option[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param Option[] $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return Validator[]
     */
    public function getValidators(): array
    {
        return $this->validators;
    }

    /**
     * @param Validator[] $validators
     */
    public function setValidators(array $validators): void
    {
        $this->validators = $validators;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function setView(string $view): void
    {
        $this->view = $view;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        foreach ($this->validators as $validator) {
            if ('required' == $validator->getType()) {
                return (bool) $validator->getValue();
            }

            if ('min_selections' == $validator->getType() && $validator->getValue() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isStoreable()
    {
        foreach ($this->options as $option) {
            if ('storeable' == $option->getIdentifier()) {
                return (bool) $option->getValue();
            }
        }

        return true;
    }
}
