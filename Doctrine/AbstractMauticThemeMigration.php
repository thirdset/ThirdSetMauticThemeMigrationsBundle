<?php

/*
 * @package     ThirdSetMauticThemeMigrationsBundle
 * @copyright   2025 Third Set Productions.
 * @author      Third Set Productions
 * @link        https://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\ThirdSetMauticThemeMigrationsBundle\Doctrine;

use Psr\Log\LogLevel;
use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Schema\Schema;
use Mautic\CoreBundle\Doctrine\AbstractMauticMigration;
use Mautic\EmailBundle\Entity\Email;

/**
 * Base abstract class for theme migrations.
 */
abstract class AbstractMauticThemeMigration extends AbstractMauticMigration
{
    /**
     * The theme that is being migrated.
     *
     * @var string
     */
    protected $theme;

    /**
     * Whether or not this is a dry run.
     *
     * @var bool
     */
    protected $dryRun;

    /**
     * The LogLevel for log messages.
     *
     * @var string
     */
    protected $logLevel;

    /**
     * The email id of the Email to migrate. If null, all Emails will be migrated.
     *
     * @var int
     */
    protected $emailId;

    /**
     * The output writer.
     *
     * @var \Doctrine\DBAL\Migrations\OutputWriter
     */
    protected $output;


    /**
     * @var \Mautic\EmailBundle\Model\EmailModel
     */
    protected $emailModel;

    /**
     * @var \Mautic\EmailBundle\Model\EmailRepository
     */
    protected $emailRepo;

    /**
     * Constructor.
     *
     * @param Version $version
     */
    public function __construct(Version $version) {
        parent::__construct($version);

        $this->output = $this->version->getConfiguration()->getOutputWriter();
        $this->logLevel = LogLevel::INFO;
    }

    /**
     * Initialization method.
     */
    public function initialize()
    {
        $this->emailModel = $this->factory->getModel('email');
        $this->emailRepo  = $this->emailModel->getRepository();
    }

    /**
     * Sets the theme.
     *
     * @param string $theme Sets the theme.
     */
    public function setTheme($theme) {
        $this->theme = $theme;
    }

    /**
     * Gets the theme.
     *
     * @return string Returns the theme.
     */
    public function getTheme() {
        return $this->theme;
    }

    /**
     * Sets whether or not this is a dry run.
     *
     * @param bool $dryRun Whether or not we are performing a dry run.
     */
    public function setDryRun($dryRun) {
        $this->dryRun = $dryRun;
    }

    /**
     * Gets whether or not this is a dry run.
     *
     * @return bool Returns whether or not this is a dry run.
     */
    public function isDryRun() {
        return $this->dryRun;
    }

    /**
     * Sets the LogLevel.
     *
     * @param string $logLevel Sets the LogLevel.
     */
    public function setLogLevel($logLevel) {
        $this->logLevel = $logLevel;
    }

    /**
     * Gets the logLevel.
     *
     * @return string Returns the logLevel.
     */
    public function getLogLevel() {
        return $this->logLevel;
    }

    /**
     * Sets the email id.
     *
     * @param int|null $emailId Sets the email id.
     */
    public function setEmailId($emailId = null) {
        $this->emailId = $emailId;
    }

    /**
     * Gets the email id.
     *
     * @return int|null Returns the email id.
     */
    public function getEmailId() {
        return $this->emailId;
    }

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
    public function preUp(Schema $schema)
    {
        $this->output->write('preUp: nothing to do');
    }

    /**
     * Implement the main changes for the migration. Generally, you just put
     * database schema changes here.
     *
     * Override this method (if needed) in your subclass.
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->output->write('up: nothing to do');
    }

    /**
     * Run any tasks/migrations that should be performed after the main 'up'
     * method.
     *
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
        $this->output->write('postUp: migrating emails'.(($this->dryRun) ? ' (DRY RUN)' : '').'...');
        $this->initialize();
        $this->doUpdateEmails();
    }

    /**
     * Protected helper method that updates the Emails.
     */
    protected function doUpdateEmails()
    {
        $q = $this->emailRepo->createQueryBuilder('e')->select('e');

        $q->where(
            $q->expr()->eq('e.template', ':template')
        )
        ->setParameter('template', $this->theme);


        if (null !== $this->emailId) {
            $q->andWhere(
                $q->expr()->eq('e.id', ':id')
            )
            ->setParameter('id', $this->emailId);
        }


        $emails = $q->getQuery()->getResult();
        if (!empty($emails)) {
            /* @var $email \Mautic\EmailBundle\Entity\Email */
            foreach ($emails as $email) {
                $this->doUpdateEmail($email);
            }
        }
    }

    /**
     * Protected helper method that updates an Email.
     *
     * @param Email $email The Email to update.
     */
    protected function doUpdateEmail(Email $email)
    {
        $this->output->write($email->getId().': '.$email->getName());
        $html = $email->getCustomHtml();

        // Update the HTML using the doUpdateEmailHtml method of the child class.
        $newHtml = $this->doUpdateEmailHtml($html);

        // Debug output.
        if (LogLevel::DEBUG === $this->logLevel) {
            $this->output->write('---------- OLD ------------');
            $this->output->write($html);
            $this->output->write('---------- NEW ------------');
            $this->output->write($newHtml);
        }

        // Update (if not dry run).
        if (!$this->dryRun) {
            $email->setCustomHtml($newHtml);
            $this->emailRepo->saveEntity($email);
        }
    }

    /**
     * Helper method that updates the HTML.
     *
     * Override this method in your subclass.
     *
     * @param string $html The HTML to update.
     * @return string Returns the updated HTML.
     */
    public function doUpdateEmailHtml($html)
    {
        throw new \Exception('Child class must override this method.');
    }
}
