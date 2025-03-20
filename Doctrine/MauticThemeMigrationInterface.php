<?php

/*
 * @package     ThirdSetMauticThemeMigrationsBundle
 * @copyright   2025 Third Set Productions.
 * @author      Third Set Productions
 * @link        https://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticThemeMigrationsBundle\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Interface for theme migrations.
 */
interface MauticThemeMigrationInterface
{
    /**
     * {@inheritdoc}
     */
    function setContainer(ContainerInterface $container = null);

    /**
     * Sets the theme.
     *
     * @param string $theme Sets the theme.
     */
    function setTheme($theme);

    /**
     * Gets the theme.
     *
     * @return string Returns the theme.
     */
    function getTheme();

    /**
     * Sets whether or not this is a dry run.
     *
     * @param bool $dryRun Whether or not we are performing a dry run.
     */
    function setDryRun($dryRun);

    /**
     * Gets whether or not this is a dry run.
     *
     * @return bool Returns whether or not this is a dry run.
     */
    function isDryRun();

    /**
     * Sets the LogLevel.
     *
     * @param string $logLevel Sets the LogLevel.
     */
    function setLogLevel($logLevel);

    /**
     * Gets the logLevel.
     *
     * @return string Returns the logLevel.
     */
    function getLogLevel();

    /**
     * Sets the email id.
     *
     * @param int|null $emailId Sets the email id.
     */
    function setEmailId($emailId = null);

    /**
     * Gets the email id.
     *
     * @return int|null Returns the email id.
     */
    function getEmailId();

    /**
     * Run any tasks/migrations that should be performed before the main 'up'
     * method.
     *
     * Override this method (if needed) in your subclass.
     *
     * @param Schema $schema
     * @throws SkipMigrationException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    function preUp(Schema $schema);

    /**
     * Implement the main changes for the migration. Generally, you just put
     * database schema changes here.
     *
     * Override this method (if needed) in your subclass.
     *
     * @param Schema $schema
     */
    function up(Schema $schema);

    /**
     * Run any tasks/migrations that should be performed after the main 'up'
     * method.
     *
     * @param Schema $schema
     */
    function postUp(Schema $schema);

    /**
     * Method that updates the email HTML.
     *
     * @param string $html The HTML to update.
     * @return string Returns the updated HTML.
     */
    function doUpdateEmailHtml($html);

}
