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
class Form
{
    public const STATUS_ARCHIVED  = 0;
    public const STATUS_PUBLISHED = 1;

    public const REDIRECT_MESSAGE = 0;
    public const REDIRECT_URL     = 1;
    public const REDIRECT_CONTENT = 2;

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
     * @Serializer\Accessor(setter="setStatus")
     */
    private $status;

    /**
     * @var User|null
     * @Serializer\Type("eZ\Publish\API\Repository\Values\User\User")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setUser")
     */
    private $user;

    /**
     * @var Version
     * @Serializer\Type("Novactive\EzFormBuilderMigration\Model\Version")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setVersion")
     */
    private $version;

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
     * @Serializer\Accessor(setter="setDescription")
     */
    private $description;

    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setSubmitText")
     */
    private $submitText;

    /**
     * @var int
     * @Serializer\Type("int")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setRedirectType")
     */
    private $redirectType;

    /**
     * @var string|null
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setRedirectUrl")
     */
    private $redirectUrl;

    /**
     * @var string|null
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setRedirectContent")
     */
    private $redirectContent;

    /**
     * @var string|null
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setThankyouText")
     */
    private $thankyouText;

    /**
     * @var string|null
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setCallbackUrl")
     */
    private $callbackUrl;

    /**
     * @var string|null
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setEmail")
     */
    private $email;

    /**
     * @var string[]
     * @Serializer\Type("array<string>")
     * @Serializer\Expose()
     * @Serializer\Accessor(setter="setEmailCc")
     */
    private $emailCc = [];

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

    /**
     * Convenient wrapper.
     *
     * @return Field[]
     *
     * @see Version::getFields()
     */
    public function getFields(): array
    {
        return $this->version->getFields();
    }

    /**
     * @param string $fieldId
     */
    public function getField($fieldId): Field
    {
        $fields = $this->version->getFields();

        return $fields[$fieldId];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function setVersion(Version $version): void
    {
        $this->version = $version;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getSubmitText(): string
    {
        return $this->submitText;
    }

    public function setSubmitText(string $submitText): void
    {
        $this->submitText = $submitText;
    }

    public function getRedirectType(): int
    {
        return $this->redirectType;
    }

    public function setRedirectType(int $redirectType): void
    {
        $this->redirectType = $redirectType;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl(?string $redirectUrl): void
    {
        $this->redirectUrl = $redirectUrl;
    }

    public function getRedirectContent(): ?string
    {
        return $this->redirectContent;
    }

    public function setRedirectContent(?string $redirectContent): void
    {
        $this->redirectContent = $redirectContent;
    }

    public function getThankyouText(): ?string
    {
        return $this->thankyouText;
    }

    public function setThankyouText(?string $thankyouText): void
    {
        $this->thankyouText = $thankyouText;
    }

    public function getCallbackUrl(): ?string
    {
        return $this->callbackUrl;
    }

    public function setCallbackUrl(?string $callbackUrl): void
    {
        $this->callbackUrl = $callbackUrl;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string[]
     */
    public function getEmailCc(): array
    {
        return $this->emailCc;
    }

    /**
     * @param string[] $emailCc
     */
    public function setEmailCc(array $emailCc): void
    {
        $this->emailCc = $emailCc;
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
}
