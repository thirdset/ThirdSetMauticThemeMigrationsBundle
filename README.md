# ThirdSetMauticThemeMigrationsBundle

## [Description](id:description)

The ThirdSetMauticThemeMigrationsBundle contains tools for making changes/updates
to your custom theme and having the changes apply to existing content (emails
and pages).

## [Compatibility](id:compatibility)

This plugin has been tested with up to v2.16.5 of Mautic.

## [Installation](id:installation)

1. Download or clone this bundle into your Mautic /plugins folder.
2. Manually delete your cache (app/cache/prod).
3. In the Mautic GUI, go to the gear and then to Plugins.
4. Click the down arrow in the top right and select "Install/Upgrade Plugins"
5. You should now see the Theme Migrations plugin in your list of plugins.

## [Usage](id:usage)

### Configure Your Migration

* Copy the example migration (in examples/migrations) to a new `migrations` folder
in your Mautic theme.
* Update the migration to suit your needs.

### Run the Commands

The plugin adds a command named `mautic:theme:migrations:migrate`.

The migrate command does the following:

* Searches for and executes the specified migration.

You can see the command options by running:

```
php app/console mautic:theme:migrations:migrate --help
```

Example (dry run the migration on a particular email (#1234) using verbose output): 

```
php app/console mautic:theme:migrations:migrate --theme="mytheme" --migration 20250317000000 --email-id 1234 --dry-run --verbose
```

## [Why Use This Plugin](id:why)

This plugin allows mautic users to update existing emails when a theme is
changed.

When you create an email in Mautic, a snapshot of the theme as it currently 
exists is used to build the email. After that point, the theme is no longer
used. Any subsequent changes to the theme only effect newly created emails.

This can be problmeatic when many emails are created from the theme and when
those emails are used by a variety of campaigns.

This plugin allows you to create a simple migration script to update your old
emails. You can then execute your migration script using a simple mautic command.

## [Future Plans](id:futureplans)

* It would be nice if the plugin was smart enough to update old emails (that 
  contain the same editable regions) as the new version of the theme without
  needing specific migration instructions. This seems pretty doable.
* It would be nice if you could specify a migration class/script outside of a
  theme and execute it on all emails (regardless of theme). This could be helpful
  for changes that span themes (such as changes to your company's business
  address, domain, etc). 

## [Credits](id:credits)

This plugin is developed and maintained by 
[Third Set Productions](https://www.thirdset.com) the makers of [AdPlugg](https://www.adplugg.com).

## [Disclaimer](id:disclaimer)

This plugin is licensed under GPLv3.

The GPL clearly explains that there is no warranty for this free software. 
Please see the included license.txt file for details.
