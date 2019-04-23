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

namespace Novactive\Bundle\EzFormBuilderMigrationBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use Novactive\EzFormBuilderMigration\Repository\FormContentService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrateFormsCommand extends Command
{
    /** @var FormContentService */
    protected $formContentService;

    /** @var Repository */
    protected $repository;

    /** @var Connection */
    protected $connection;

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
    public function setFormContentService(FormContentService $formContentService): void
    {
        $this->formContentService = $formContentService;
    }

    /**
     * @required
     */
    public function setRepository(Repository $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('form_builder_migration:migrate')
            // phpcs:disable
            ->setDescription(<<<'EOF'
            
<info>%command.name%</info> runs a migration process for eZStudio Form builder forms created in previous versions of eZ Platform. 

<warning>During the script execution the database should not be modified.

To avoid surprises you are advised to create a backup or execute a dry run:
 
    %command.name% --dry-run
    
before proceeding with the actual update.</warning>

Since this script can potentially run for a very long time, to avoid memory
exhaustion, run it in production environment using the <info>--env=prod</info> switch.

If you configuration uses multiple repositories, 
you have to run the comand multiple times 
with different siteaccesses using <info>--siteaccess</info> switch.

EOF
            )
            ->addOption(
                'content_type_identifier',
                null,
                InputOption::VALUE_OPTIONAL,
                'Identifier for the content type to use for creating content',
                'form'
            )
            ->addOption(
                'field_identifier',
                null,
                InputOption::VALUE_OPTIONAL,
                'Identifier for the content type field to use for storing the forms',
                'form'
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'When specified, changes are _NOT_ persisted to database.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->formContentService->setOptions([
            'content_type_identifier' => $input->getOption('content_type_identifier'),
            'field_identifier'        => $input->getOption('field_identifier'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $isDryRun = $input->getOption('dry-run');
        $io       = new SymfonyStyle($input, $output);
        $query    = $this->connection->createQueryBuilder();

        $query->select('f.id, f.name, f.status, a.language_code')
            ->from('ezformbuilder_form', 'f')
            ->leftJoin(
                'f',
                'ezformbuilder_form_content_relation',
                'fr',
                $query->expr()->eq('fr.form_id', 'f.id')
            )
            ->leftJoin(
                'f',
                'ezcontentobject_attribute',
                'a',
                $query->expr()->andX(
                    $query->expr()->eq('a.data_type_string', '"ezlandingpage"'),
                    $query->expr()->like('a.data_text', 'CONCAT("%", fr.block_id, "%")')
                )
            )
            ->groupBy('f.id, a.language_code');

        $statement = $query->execute();
        $results   = $statement instanceof Statement ? $statement->fetchAll() : null;

        if (empty($results)) {
            $io->writeln('Found 0 ezstudio forms. Exiting...');

            return null;
        }

        $forms = [];
        foreach ($results as $result) {
            $formId         = $result['id'];
            $forms[$formId] = $forms[$formId] ?? [
                    'id'        => $result['id'],
                    'name'      => $result['name'],
                    'status'    => $result['status'],
                    'languages' => [],
                ];

            if (null !== $result['language_code']) {
                $forms[$formId]['languages'][] = $result['language_code'];
            }
        }
        $io->warning('You are about to run data migration process for eZStudio Forms. This operation cannot be reverted.');

        $question = sprintf('Found %d formbuilder form items. Do you want to continue?', count($forms));
        if (!$io->confirm($question, false)) {
            return null;
        }

        if (!$isDryRun) {
            $this->connection->beginTransaction();
        }

        $progressBar = new ProgressBar($output, count($forms));
        $this->repository->sudo(function () use ($progressBar, $forms) {
            foreach ($forms as $form) {
                $ezStudioFormId = (int) $form['id'];

                try {
                    $this->formContentService->loadContent($ezStudioFormId);
                } catch (NotFoundException $exception) {
                    $this->formContentService->createContent($ezStudioFormId, $form['languages']);
                }

                $progressBar->advance();
            }
        });
        $progressBar->finish();
        $io->newLine();

        $query    = $this->connection->createQueryBuilder();
        $query->select('a.id as attributeId, a.name, a.value as formId, b.id blockId, b.type')
            ->from('ezpage_blocks', 'b')
            ->innerJoin('b', 'ezpage_map_attributes_blocks', 'ba', $query->expr()->eq('ba.block_id', 'b.id'))
            ->innerJoin('b', 'ezpage_attributes', 'a', $query->expr()->eq('a.id', 'ba.attribute_id'))
            ->where(
                $query->expr()->eq('b.type', ':blockType'),
                $query->expr()->eq('a.name', ':attributeName')
            )
        ->setParameter(':blockType', 'formbuilder', ParameterType::STRING)
        ->setParameter(':attributeName', 'formId', ParameterType::STRING);

        $statement = $query->execute();
        $blocks    = $statement instanceof Statement ? $statement->fetchAll() : [];

        $io->writeln(sprintf('Found %d formbuilder block items. ', count($blocks)));
        $progressBar = new ProgressBar($output, count($blocks));
        foreach ($blocks as $block) {
            $content = $this->formContentService->loadContent((int)$block['formId']);

            $updateAttributQuery = $this->connection->createQueryBuilder();
            $updateAttributQuery->update('ezpage_attributes')
                ->set('name', ':attributeName')
                ->set('value', ':attributeValue')
                ->where('id', ':attributeId')
                ->setParameter(':attributeName', 'contentId', ParameterType::STRING)
                ->setParameter(':attributeValue', $content->id, ParameterType::INTEGER)
                ->setParameter(':attributeId', $block['attributeId'], ParameterType::INTEGER);
            $updateAttributQuery->execute();

            $updateBlockQuery = $this->connection->createQueryBuilder();
            $updateBlockQuery->update('ezpage_blocks')
                ->set('type', ':blockType')
                ->where('id', ':blockId')
                ->setParameter(':blockType', 'form', ParameterType::STRING)
                ->setParameter(':blockId', $block['blockId'], ParameterType::INTEGER);
            $updateBlockQuery->execute();

            $progressBar->advance();
        }
        $progressBar->finish();
        $io->newLine();

        if (!$isDryRun) {
            $this->connection->commit();
        }
    }
}
