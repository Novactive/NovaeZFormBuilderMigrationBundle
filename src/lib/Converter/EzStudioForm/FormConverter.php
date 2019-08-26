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
use EzSystems\EzPlatformFormBuilder\FieldType\Model\Attribute;
use EzSystems\EzPlatformFormBuilder\FieldType\Model\Field;
use EzSystems\EzPlatformFormBuilder\FieldType\Model\Form;
use EzSystems\EzPlatformFormBuilder\FieldType\Value as FormValue;
use Novactive\EzFormBuilderMigration\Converter\EzStudioForm\FieldConverter as EzStudioFormFieldConverter;
use Novactive\EzFormBuilderMigration\GuidGenerator;
use Novactive\EzFormBuilderMigration\Model\Form as EzStudioForm;

class FormConverter
{
    /** @var EzStudioFormFieldConverter */
    protected $ezStudioFormFieldConverter;

    /**
     * @required
     */
    public function setEzStudioFormFieldConverter(EzStudioFormFieldConverter $ezStudioFormFieldConverter): void
    {
        $this->ezStudioFormFieldConverter = $ezStudioFormFieldConverter;
    }

    /**
     * @throws NotFoundException
     */
    public function convert(EzStudioForm $eZStudioForm): FormValue
    {
        $fields         = [];
        $ezStudioFields = $eZStudioForm->getFields();
        foreach ($ezStudioFields as $ezStudioField) {
            $fields[] = $this->ezStudioFormFieldConverter->convert($ezStudioField);
        }

        $submitOptions = [
            'action'      => 'message',
            'location_id' => $eZStudioForm->getRedirectContent(),
            'url'         => $eZStudioForm->getRedirectUrl(),
            'message'     => $eZStudioForm->getThankyouText(),
        ];

        if ($submitOptions['location_id'] !== '') {
            $submitOptions['action'] = 'location_id';
        }
        if ($submitOptions['url'] !== '') {
            $submitOptions['location_id'] = '';
            $submitOptions['action'] = 'url';
        }

        $actionValue = json_encode($submitOptions);
        $fields[]    = new Field(
            GuidGenerator::generate('fbf-'),
            'button',
            $eZStudioForm->getSubmitText(),
            [
                new Attribute(
                    'notification_email',
                    $eZStudioForm->getEmail()
                ),
                new Attribute(
                    'action',
                    false !== $actionValue ? $actionValue : null
                ),
            ]
        );

        $form = new Form();
        $form->setFields($fields);

        return new FormValue($form);
    }
}
