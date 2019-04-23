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

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\Core\Base\Exceptions\NotFoundException as NotFound;
use EzSystems\EzPlatformFormBuilder\FieldType\Value as FormValue;
use Novactive\EzFormBuilderMigration\Converter\EzStudioForm\FormConverter as EzStudioFormConverter;
use Novactive\EzFormBuilderMigration\Model\Form as EzStudioForm;
use Novactive\EzFormBuilderMigration\Persistence\EzStudioFormPersistenceHandler;

class EzStudioFormService
{
    /** @var EzStudioFormConverter */
    protected $converter;

    /** @var EzStudioFormPersistenceHandler */
    protected $persistenceHandler;

    /**
     * @required
     */
    public function setPersistenceHandler(EzStudioFormPersistenceHandler $persistenceHandler): void
    {
        $this->persistenceHandler = $persistenceHandler;
    }

    /**
     * @required
     */
    public function setConverter(EzStudioFormConverter $converter): void
    {
        $this->converter = $converter;
    }

    /**
     * @throws NotFound
     * @throws NotFoundException
     */
    public function load(int $formId, ?int $versionNo = null): EzStudioForm
    {
        return $this->persistenceHandler->loadForm($formId, $versionNo);
    }

    /**
     * @throws NotFound
     */
    public function toFormValue(EzStudioForm $ezStudioForm): FormValue
    {
        return $this->converter->convert($ezStudioForm);
    }
}
