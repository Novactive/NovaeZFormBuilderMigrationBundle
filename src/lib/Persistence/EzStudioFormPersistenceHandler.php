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

namespace Novactive\EzFormBuilderMigration\Persistence;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException as NotFound;
use EzSystems\EzPlatformFormBuilder\FieldType\Converter\FormConverter as EzFormConverter;
use JMS\Serializer\Serializer;
use Novactive\EzFormBuilderMigration\Converter\EzStudioForm\FieldConverter as EzStudioFormFieldConverter;
use Novactive\EzFormBuilderMigration\Model\Form;

class EzStudioFormPersistenceHandler
{
    /** @var Connection */
    protected $connection;

    /** @var UserService */
    protected $userService;

    /** @var Serializer */
    protected $serializer;

    /** @var EzFormConverter */
    protected $ezFormConverter;

    /** @var EzStudioFormFieldConverter */
    protected $ezStudioFormFieldConverter;

    /**
     * @required
     */
    public function setConnection(Connection $connection): void
    {
        $this->connection = $connection;
    }

    /**
     * @required
     */
    public function setUserService(UserService $userService): void
    {
        $this->userService = $userService;
    }

    /**
     * @required
     */
    public function setSerializer(Serializer $serializer): void
    {
        $this->serializer = $serializer;
    }

    /**
     * @throws NotFound
     * @throws NotFoundException
     */
    public function loadForm(int $formId, ?int $versionNo = null): Form
    {
        $formHash    = $this->innerLoadForm($formId);
        $versionHash = $this->innerLoadVersion((int) $formHash['id'], $versionNo ?: (int) $formHash['version']);

        return $this->fromPersistenceValue($formHash, $versionHash);
    }

    /**
     * @throws NotFound
     */
    protected function innerLoadForm(int $formId): array
    {
        $query = $this->connection->createQueryBuilder();

        $query->select('*')
            ->from('ezformbuilder_form')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter(':id', $formId, ParameterType::INTEGER);
        $query->setMaxResults(1);
        $statement = $query->execute();
        $form      = $statement instanceof Statement ? $statement->fetch(FetchMode::ASSOCIATIVE) : null;

        if (empty($form)) {
            throw new NotFound('ezstudio form', sprintf('id: %d', $formId));
        }

        return $form;
    }

    /**
     * @throws NotFound
     */
    public function innerLoadVersion(int $formId, int $versionNo): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id, form_id, user_id, version, checksum, structure, created, updated')
            ->from('ezformbuilder_form_version')
            ->where(
                $query->expr()->eq('form_id', ':formId'),
                $query->expr()->eq('version', ':version')
            )
            ->setParameter(':formId', $formId, ParameterType::INTEGER)
            ->setParameter(':version', $versionNo, ParameterType::INTEGER);
        $query->setMaxResults(1);
        $statement = $query->execute();
        $version   = $statement instanceof Statement ? $statement->fetch(FetchMode::ASSOCIATIVE) : null;
        if (empty($version)) {
            throw new NotFound('ezstudio form version', sprintf('formId: %d, version: %d', $formId, $versionNo));
        }

        return $version;
    }

    /**
     * @throws NotFoundException
     */
    public function fromPersistenceValue(array $formHash, array $versionHash): Form
    {
        $pages = json_decode($versionHash['structure'], true);
        foreach ($pages as $pageId => $page) {
            foreach ($page['fields'] as $fieldId => $field) {
                foreach ($field['options'] as $optionId => $option) {
                    $pages[$pageId]['fields'][$fieldId]['options'][$optionId]['value'] =
                        is_array($option['value']) ?
                            json_encode($option['value']) :
                            $option['value'];
                }
                foreach ($field['validators'] as $validatorId => $validator) {
                    $pages[$pageId]['fields'][$fieldId]['validators'][$validatorId]['value'] =
                        is_array($validator['value']) ?
                            json_encode($validator['value']) :
                            $validator['value'];
                }
            }
        }

        $formName = trim($formHash['name']);
        /** @var Form $form */
        $form = $this->serializer->fromArray(
            [
                'id'               => $formHash['id'],
                'name'             => !empty($formName) ? $formName : "Form {$formHash['id']}",
                'user'             => null,
                'description'      => $formHash['description'],
                'submit_text'      => trim($formHash['submit_text']),
                'redirect_type'    => $formHash['redirect_type'],
                'redirect_url'     => $formHash['redirect_url'],
                'redirect_content' => $formHash['redirect_content'],
                'thankyou_text'    => $formHash['thankyou_text'],
                'callback_url'     => $formHash['callback_url'],
                'email'            => $formHash['email'],
                'emailCc'          => array_filter(json_decode($formHash['email_cc'])),
                'status'           => $formHash['status'],
                'created'          => date(DateTime::ATOM, (int) $formHash['created']),
                'updated'          => date(DateTime::ATOM, (int) $formHash['updated']),
                'version'          => [
                    'id'        => $versionHash['id'],
                    'user'      => null,
                    'form_id'   => $versionHash['form_id'],
                    'checksum'  => $versionHash['checksum'],
                    'pages'     => $pages,
                    'version'   => $versionHash['version'],
                    'created'   => date(DateTime::ATOM, (int) $versionHash['created']),
                    'updated'   => date(DateTime::ATOM, (int) $versionHash['updated']),
                ],
            ],
            Form::class
        );

        $form->setUser($this->userService->loadUser($formHash['user_id']));
        $form->getVersion()->setUser($this->userService->loadUser($versionHash['user_id']));

        return $form;
    }
}
