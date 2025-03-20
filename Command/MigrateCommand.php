<?php

namespace MauticPlugin\ThirdSetMauticThemeMigrationsBundle\Command;

use Mautic\CoreBundle\Command\ModeratedCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use MauticPlugin\ThirdSetMauticThemeMigrationsBundle\Helper\ThemeMigrationsHelper;

/**
 * The MigrateCommand executes the specified migration.
 *
 * @package ThirdSetMauticThemeMigrationsBundle
 * @since 1.0.0
 */
class MigrateCommand extends ModeratedCommand
{
    /**
     * @var \MauticPlugin\ThirdSetMauticThemeMigrationsBundle\Helper\ThemeMigrationsHelper
     */
    private $migrationsHelper;

    /**
     * Constructor.
     *
     * @param ThemeMigrationsHelper $migrationsHelper
     */
    public function __construct(ThemeMigrationsHelper $migrationsHelper) {
        parent::__construct();

        $this->migrationsHelper = $migrationsHelper;
    }

    /**
     * Configures the command.
     */
    protected function configure()
    {
        $this
            ->setName('mautic:theme:migrations:migrate')
            ->setDescription('Performs the specified theme migration.')
            ->addOption(
                '--theme',
                '-t',
                InputOption::VALUE_REQUIRED,
                'The name of the theme to migrate.',
                null
            )
            ->addOption(
                '--migration',
                '-m',
                InputOption::VALUE_REQUIRED,
                'The name of the migration (YYYYMMDDHHMMSS).',
                null
            )
            ->addOption(
                '--email-id',
                null,
                InputOption::VALUE_REQUIRED, // Value required when option set.
                'The id of the email to migrate. If not included, all emails will be migrated.',
                null
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Set --dry-run to output what the command would do without actually doing anything.'
            );
        ;
    }

    /**
     * Executes the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $theme = $input->getOption('theme');
        $migrationVersion = (null !== $input->getOption('migration')) ? intval($input->getOption('migration')) : null;
        $emailId = (null !== $input->getOption('email-id')) ? intval($input->getOption('email-id')) : null;
        $dryRun = ($input->getOption('dry-run')) ? true : false;

        // ------ VALIDATE THE PASSED OPTIONS ------ //
        if (null === $theme) {
            throw new \InvalidArgumentException('Must specify a theme (use --theme).');
        }
        if (null === $migrationVersion) {
            throw new \InvalidArgumentException('Must specify a migration version (use --migration YYYYMMDDHHMMSS).');
        }

        if ($dryRun) {
            $output->writeln('DRY RUN...');
        } else {
            $output->writeln('Starting...');
        }

        // Do the migration.
        $this->migrationsHelper->migrate(
            $output,
            $theme,
            $migrationVersion,
            $emailId,
            $dryRun
        );

        $output->writeln('Done.');
    }
}
