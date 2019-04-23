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

namespace Novactive\EzFormBuilderMigration\Repository;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\API\Repository\Values\Content\LocationCreateStruct;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Novactive\EzFormBuilderMigration\Model\Form as EzStudioForm;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\NoSuchOptionException;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormContentService
{
    /** @var ContentTypeService */
    protected $contentTypeService;

    /** @var ContentService */
    protected $contentService;

    /** @var EzStudioFormService */
    protected $ezStudioFormService;

    /** @var ConfigResolverInterface */
    protected $configResolver;

    /** @var array */
    protected $options;

    /**
     * FormContentService constructor.
     *
     *
     * @throws AccessException
     * @throws InvalidOptionsException
     * @throws MissingOptionsException
     * @throws NoSuchOptionException
     * @throws OptionDefinitionException
     * @throws UndefinedOptionsException
     */
    public function __construct(array $options)
    {
        $this->setOptions($options);
    }

    public function setOptions(array $options)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * @throws AccessException
     * @throws UndefinedOptionsException
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('content_type_identifier');
        $resolver->setRequired('field_identifier');
        $resolver->setAllowedTypes('content_type_identifier', 'string');
        $resolver->setAllowedTypes('field_identifier', 'string');
    }

    /**
     * @required
     */
    public function setContentTypeService(ContentTypeService $contentTypeService): void
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @required
     */
    public function setContentService(ContentService $contentService): void
    {
        $this->contentService = $contentService;
    }

    /**
     * @required
     */
    public function setEzStudioFormService(EzStudioFormService $ezStudioFormService): void
    {
        $this->ezStudioFormService = $ezStudioFormService;
    }

    /**
     * @required
     */
    public function setConfigResolver(ConfigResolverInterface $configResolver): void
    {
        $this->configResolver = $configResolver;
    }

    /**
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\Core\Base\Exceptions\NotFoundException
     *
     * @return Content
     */
    public function createContent(int $ezStudioFormId, array $languages)
    {
        $ezStudioForm = $this->ezStudioFormService->load($ezStudioFormId);

        /* Assigning the content locations */
        $locationCreateStructs = [
            new LocationCreateStruct([
                'parentLocationId' => $this->getParentLocationId(),
            ]),
        ];

        $contentCreateStruct = $this->getContentCreateStructure($ezStudioForm, $languages);

        /* Creating new draft */
        $draft = $this->contentService->createContent($contentCreateStruct, $locationCreateStructs);

        /* Publish the new content draft */
        $content = $this->contentService->publishVersion($draft->versionInfo);

        return $content;
    }

    protected function getParentLocationId(): int
    {
        return $this->configResolver->getParameter('form_builder.forms_location_id');
    }

    /**
     * @throws NotFoundException
     * @throws \eZ\Publish\Core\Base\Exceptions\NotFoundException
     */
    protected function getContentCreateStructure(EzStudioForm $ezStudioForm, array $languages): ContentCreateStruct
    {
        $contentType      = $this->getContentType();
        $mainLanguageCode = reset($languages);

        /* Creating new content create structure */
        $contentCreateStruct = $this->contentService->newContentCreateStruct(
            $contentType,
            $mainLanguageCode
        );

        $contentCreateStruct->remoteId = $this->generateRemoteId($ezStudioForm->getId());
        $contentCreateStruct->ownerId  = (null !== $user = $ezStudioForm->getUser()) ? $user->getUserId() : null;

        /* Update content structure fields */
        $fieldDefinitions = $contentType->getFieldDefinitions();
        foreach ($fieldDefinitions as $fieldDefinition) {
            $fieldValue = null;
            if (strpos($contentType->nameSchema, $fieldDefinition->identifier)) {
                $fieldValue = $ezStudioForm->getName();
            }
            if ($fieldDefinition->identifier === $this->options['field_identifier']) {
                $fieldValue = $this->ezStudioFormService->toFormValue($ezStudioForm);
            }
            foreach ($languages as $language) {
                $contentCreateStruct->setField(
                    $fieldDefinition->identifier,
                    $fieldValue,
                    $language
                );
            }
        }

        return $contentCreateStruct;
    }

    /**
     * @throws NotFoundException
     */
    public function getContentType(): ContentType
    {
        return $this->contentTypeService->loadContentTypeByIdentifier($this->options['content_type_identifier']);
    }

    protected function generateRemoteId(int $ezStudioFormId)
    {
        return sprintf('ezstudioform-%d', $ezStudioFormId);
    }

    /**
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function loadContent(int $ezStudioFormId): Content
    {
        return $this->contentService->loadContentByRemoteId(
            $this->generateRemoteId($ezStudioFormId)
        );
    }
}
