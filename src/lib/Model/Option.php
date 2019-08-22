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
class Option
{
    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setIdentifier")
     */
    private $identifier;

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
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setValue")
     */
    private $value;

    /**
     * @var bool
     * @Serializer\Type("bool")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setRequired")
     */
    private $required;

    /**
     * @var bool
     * @Serializer\Type("bool")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setHidden")
     */
    private $hidden;

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
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

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }
}
