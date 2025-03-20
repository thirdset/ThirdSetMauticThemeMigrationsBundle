<?php

/*
 * @package     ThirdSetMauticThemeMigrationsBundle
 * @copyright   2025 Third Set Productions.
 * @author      Third Set Productions
 * @link        https://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticThemeMigrationsBundle\Helper;

use Psr\Log\LogLevel;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Migrations\OutputWriter;

/**
 * Class ThemeMigrationsHelper.
 *
 * @package ThirdSetMauticThemeMigrationsBundle
 * @since 1.0.0
 */
class ThemeMigrationsHelper
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param EntityManager $em
     */
    public function __construct(
        ContainerInterface $container,
        EntityManager $em
    ) {
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * Performs a migration.
     *
     * @param OutputInterface $output           The OutputInterface (for logging).
     * @param string          $themeName        The name of the theme to migrate.
     * @param int             $migrationVersion The migration Version as an int, formatted as (YYYYMMDDHHMMSS).
     * @param int|null        $emailId          The id of the Email to migrate. If null, all Emails will be migrated.
     * @param bool            $dryRun           If true, output what the method would
     *                                          do without actually doing anything.
     */
    public function migrate(
        OutputInterface $output,
        $themeName,
        $migrationVersion,
        $emailId = null,
        $dryRun = false
    ) {
        $schema = $this->getSchema();

        if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
            $logLevel = LogLevel::DEBUG;
        }

        $filename = 'Version'.$migrationVersion.'.php';
        $path = 'themes/'.$themeName.'/migrations/'.$filename;
        $filepath = '/../../../'.$path;
        $output->writeln(
            'Searching for migration "'.$migrationVersion.'" in theme "'
                .$themeName.'" at "'.$path.'"...'
        );

        require_once(__DIR__.$filepath);

        $output->writeln('Migration found and loaded.');

        // Set up the migration.
        $class = '\Mautic\Migrations\Version'.$migrationVersion;
        $configuration = $this->getConfiguration($output);
        $version = new Version($configuration, $version, $class);

        /* @var $migration \MauticPlugin\ThirdSetMauticThemeMigrationsBundle\Doctrine\MauticThemeMigrationInterface */
        $migration = new $class($version);
        $migration->setContainer($this->container);
        $migration->setTheme($themeName);
        $migration->setEmailId($emailId);
        $migration->setLogLevel($logLevel);
        $migration->setDryRun($dryRun);

        // Execute the migration.
        $migration->preUp($schema);
        $migration->up($schema);
        $migration->postUp($schema);
    }

    /**
     * Private helper method that gets the current Schema.
     *
     * @returns Schema Returns the current Schema.
     */
    private function getSchema()
    {
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        if (empty($metadata)) {
            throw new \UnexpectedValueException('No mapping information to process');
        }

        $tool = new SchemaTool($this->em);

        return $tool->getSchemaFromMetadata($metadata);
    }

    /**
     * Private helper method that gets the migration Configuration
     *
     * @returns Configuration Returns the migration Configuration.
     */
    private function getConfiguration(OutputInterface $outputInterface)
    {
        $configuration = new Configuration($this->em->getConnection());
        $configuration->setName('Theme Migrations');

        // Set up the output writer.
        $closure = function($message) use ($outputInterface) {
            $outputInterface->writeln($message);
        };
        $outputWriter = new OutputWriter($closure);
        $configuration->setOutputWriter($outputWriter);

        return $configuration;
    }

}
