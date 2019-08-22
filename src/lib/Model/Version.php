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

use DateTime;
use eZ\Publish\API\Repository\Values\User\User;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\AccessType("public_method")
 */
class Version
{
    /**
     * @var int
     * @Serializer\Type("int")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setId")
     */
    private $id;

    /**
     * @var int
     * @Serializer\Type("int")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setFormId")
     */
    private $formId;

    /**
     * @var User|null
     * @Serializer\Type("eZ\Publish\API\Repository\Values\User\User")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setUser")
     */
    private $user;

    /**
     * @var int
     * @Serializer\Type("int")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setVersion")
     */
    private $version;

    /**
     * @var Page[]
     * @Serializer\Type("array<Novactive\EzFormBuilderMigration\Model\Page>")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setPages")
     */
    private $pages;

    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setChecksum")
     */
    private $checksum;

    /**
     * @var DateTime
     * @Serializer\Type("DateTime")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setCreated")
     */
    private $created;

    /**
     * @var DateTime
     * @Serializer\Type("DateTime")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setUpdated")
     */
    private $updated;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getFormId(): int
    {
        return $this->formId;
    }

    public function setFormId(int $formId): void
    {
        $this->formId = $formId;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): void
    {
        $this->version = $version;
    }

    /**
     * @return Page[]
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    /**
     * @param Page[] $pages
     */
    public function setPages(array $pages): void
    {
        $this->pages = $pages;
    }

    public function getChecksum(): string
    {
        return $this->checksum;
    }

    public function setChecksum(string $checksum): void
    {
        $this->checksum = $checksum;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
    }

    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    public function setUpdated(DateTime $updated): void
    {
        $this->updated = $updated;
    }

    /**
     * @return Field[]|array
     */
    public function getFields(): array
    {
        $fields = [];

        foreach ($this->pages as $page) {
            foreach ($page->getFields() as $field) {
                $fields[$field->getId()] = $field;
            }
        }

        return $fields;
    }
}
