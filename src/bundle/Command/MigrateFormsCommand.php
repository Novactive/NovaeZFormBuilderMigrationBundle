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
            ->setName('form_builder_migration:migrate_forms_command')
            // phpcs:disable
            ->setDescription('Convert ezstudio form builder forms into ezplatform form builder form (submissions aren\'t converted yet')
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
                $query->expr()->like('a.data_text', 'CONCAT("%", fr.block_id, "%")')
            )
            ->where($query->expr()->eq('a.data_type_string', '"ezlandingpage"'))
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

            $forms[$formId]['languages'][] = $result['language_code'] ?? null;
        }

        $question = sprintf('Found %d formbuilder block items. Do you want to continue?', count($forms));
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

        if (!$isDryRun) {
            $this->connection->commit();
        }
    }
}
