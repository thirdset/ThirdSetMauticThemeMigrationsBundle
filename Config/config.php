<?php
/**
 * @package     ThirdSetMauticThemeMigrationsBundle
 * @copyright   2025 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        https://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

return array(
    'name'        => 'Theme Migrations',
    'description' => 'Allows for migrating existing emails to use the latest version of the theme.',
    'version'     => '0.1.0',
    'author'      => 'Third Set Productions',
    'services'    => array(
        'other'   => array(
            // HELPERS.
            'plugin.thirdset.theme_migrations.migrations_helper' => array(
                'class'     => 'MauticPlugin\ThirdSetMauticThemeMigrationsBundle\Helper\ThemeMigrationsHelper',
                'arguments' => [
                    'service_container',
                    'doctrine.orm.entity_manager',
                ],
            ),
            // COMMANDS.
            'plugin.thirdset.theme_migrations.migrate_command' => array(
                'class'     => 'MauticPlugin\ThirdSetMauticThemeMigrationsBundle\Command\MigrateCommand',
                'arguments' => [
                    'plugin.thirdset.theme_migrations.migrations_helper',
                ],
                'tag'       => 'console.command',
            ),
        )
    ),
);