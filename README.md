# DataLoaders for caching catalog data from the Ticket Evolution API

## Deprecated
As of November 10, 2015 this project is deprecated and you should use [TEvo Harvester](https://github.com/jwcobb/tevo-harvester) instead.

## Release Information
See [CHANGELOG.md](https://github.com/jwcobb/ticketevolution-php-dataloaders/blob/master/CHANGELOG.md)


## Installation
1. Download and extract to the folder where you want it.

2. Copy `/application/config.sample.php` to `/application/config.php` and edit to fill in your own credentials for each environment.

3. If you do not yet have Composer installed, you can follow these [directions for installing Composer](http://getcomposer.org/doc/00-intro.md#installation-nix).

4. Once you have Composer installed simply `cd` to your directory for this project and execute `composer install`

5. Run the SQL in `/scripts` starting with `create_tables.mysql` and then running any additional scripts in chronological order by the date in the filename.
