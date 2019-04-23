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
 * Class Page.
 *
 * @package Novactive\EzFormBuilderMigration\Model
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\AccessType("public_method")
 */
class Page
{
    /**
     * @var Field[]
     * @Serializer\Type("array<Novactive\EzFormBuilderMigration\Model\Field>")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setFields")
     */
    private $fields = [];

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param Field[] $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }
}
